<?php

namespace App\Jobs\AIBot;

use App\Helpers\BotPrompt;
use App\Models\AIBot;
use App\Models\Channel;
use App\Services\ContentFormatter;
use App\Services\OpenAI\DalleBot;
use App\Services\OpenAI\GPT3TurboBot;
use App\Services\Telegram\TelegramMessageService;
use App\Services\Unsplash\PhotoStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class BotSendPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private AIBot $bot,
        private Channel $channel,
        private GPT3TurboBot $service,
        private Api $telegram,
        private ContentFormatter $formatter
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = BotPrompt::prepareMessages($this->bot);
        Log::info('BotSendPost', ['messages' => $messages]);
        $generatedText = $this->service->sendMessage($messages);

        if ($this->bot->is_real_photos || $this->bot->is_generated_photos) {
            $subjectMessages = BotPrompt::textSubjectMessages($generatedText);
            $subject = $this->service->sendMessage($subjectMessages);
            if ($this->bot->is_generated_photos) {
                $dalle = $this->makeDalleBot();
                $photoUrl = $dalle->generatePhoto($subject);
            } elseif ($this->bot->is_real_photos) {
                $photoStore = $this->makePhotoStore();
                $photoUrl = $photoStore->getPhoto($subject);
                if ($photoUrl === null) {
                    unset($photoUrl);
                }
            }
        }


        try {
            DB::beginTransaction();
            $post = $this->channel->posts()->create([
                'content_html' => $generatedText,
                'content_json' => '[]', // No need to convert for now
            ]);

            $formattedContent = $this->formatter->htmlToTelegram($generatedText);

            // Send to Telegram
            if (isset($photoUrl)) {
                $message = $this->telegram->sendPhoto([
                    'chat_id' => $this->channel->telegram_chat_id,
                    'photo' => InputFile::create($photoUrl),
                    'caption' => $formattedContent,
                    'parse_mode' => 'HTML'
                ]);
            } else {
                $message = $this->telegram->sendMessage([
                    'chat_id' => $this->channel->telegram_chat_id,
                    'text' => $formattedContent,
                    'parse_mode' => 'HTML'
                ]);
            }


            // Add message ID
            $post->update([
                'telegram_message_id' => $message->messageId
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BotSendPost', ['error' => $e->getMessage()]);
            throw $e;
        }


        // Remove cache on success
        $key = 'openai_message_' . md5(serialize($messages));
        if (cache()->has($key)) {
            cache()->forget($key);
        }
    }

    private function makeDalleBot(): DalleBot
    {
        return new DalleBot(env('OPENAI_API_SECRET'));
    }

    private function makePhotoStore(): PhotoStore
    {
        return new PhotoStore();
    }
}
