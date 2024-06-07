<?php

namespace App\Jobs\Telegram;

use App\Enums\FileType;
use App\Models\Post;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\InputMedia\InputMedia;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;
use Telegram\Bot\Objects\Message;

class SendPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Api $telegram, private Post $post, private string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (count($this->post->media) === 0) {
                $message = $this->sendMessage();
            } else {
                $message = $this->sendMedia();
            }

            $this->post->update([
                'telegram_message_id' => $message->messageId
            ]);
        } catch (Exception $e) {
            Log::channel('telegram')->error($e->getMessage());
            throw $e;
        }
    }

    private function sendMessage(): Message
    {
        return $this->telegram->sendMessage([
            'chat_id' => $this->post->channel->telegram_chat_id,
            'text' => $this->message,
            'parse_mode' => 'HTML'
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    private function sendMedia(): Message
    {
        $media = $this->post->media;

        // if media is 1
        if (count($media) === 1) {
            $media = $media[0];
            $name = $media['name'];
            $type = $media['type'];
            $path = $media['path'];


            $file = InputFile::create(storage_path('app/' . $path), $name);

            return match ($type) {
                'photo' => $this->telegram->sendPhoto([
                    'chat_id' => $this->post->channel->telegram_chat_id,
                    'photo' => $file,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]),
                'audio' => $this->telegram->sendAudio([
                    'chat_id' => $this->post->channel->telegram_chat_id,
                    'audio' => $file,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]),
                'video' => $this->telegram->sendVideo([
                    'chat_id' => $this->post->channel->telegram_chat_id,
                    'video' => $file,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]),
                default => $this->telegram->sendDocument([
                    'chat_id' => $this->post->channel->telegram_chat_id,
                    'document' => $file,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]),
            };
        }

        // if media is more than 1
        // Array of InputMediaAudio, InputMediaDocument, InputMediaPhoto and InputMediaVideo

        // if all media are photos

        /*$mediaType = null;
        foreach ($media as $file) {
            $type = $file['type'];
            if ($mediaType === null) {
                $mediaType = $type;
            } else {
                if ($mediaType !== $type) {
                    $mediaType = FileType::DOCUMENT;
                    break;
                }
            }
        }*/

        // if media type is photo then send mediagroup with InputMediaPhoto
        $mediaGroup = [];
        $index = 0;
        foreach ($media as $file) {
            if ($index === 0) {
                $mediaGroup[] = [
                    'type' => $file['type'],
                    'media' => 'attach://file' . $index,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ];
            } else {
                $mediaGroup[] = [
                    'type' => $file['type'],
                    'media' => 'attach://file' . $index,
                ];
            }
            $index++;
        }

        $data = [
            'chat_id' => $this->post->channel->telegram_chat_id,
            'media' => json_encode($mediaGroup),
        ];

        foreach ($media as $index => $file) {
            $data['file' . $index] = InputFile::create(storage_path('app/' . $file['path']), $file['name']);
        }

        return $this->telegram->sendMediaGroup($data);
    }
}
