<?php

namespace App\Jobs\Telegram;

use App\Models\Post;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class UpdatePost implements ShouldQueue
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
            $this->telegram->editMessageText([
                'chat_id' => $this->post->channel->telegram_chat_id,
                'message_id' => $this->post->telegram_message_id,
                'text' => $this->message,
                'parse_mode' => 'HTML',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->telegram->editMessageCaption([
                'chat_id' => $this->post->channel->telegram_chat_id,
                'message_id' => $this->post->telegram_message_id,
                'caption' => $this->message,
                'parse_mode' => 'HTML',
            ]);
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
