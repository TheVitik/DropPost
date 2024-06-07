<?php

namespace App\Services\Telegram;

use App\Jobs\Telegram\DeletePost;
use App\Jobs\Telegram\SendPost;
use App\Jobs\Telegram\UpdatePost;
use App\Models\Post;
use Carbon\Carbon;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramMessageService extends TelegramBase
{
    /**
     * Send message to Telegram chat
     */
    public function sendPost(Post $post, string $message): void
    {
        SendPost::dispatch($this->telegram, $post, $message);
    }

    /**
     * Send message to Telegram chat at specific time
     */
    public function sendPostLater(Post $post, string $message, Carbon $publishAt): void
    {
        SendPost::dispatch($this->telegram, $post, $message)->delay($publishAt);
    }

    /**
     * Send message to Telegram chat
     */
    public function updatePost(Post $post, string $message): void
    {
        UpdatePost::dispatch($this->telegram, $post, $message);
    }

    /**
     * Delete message from Telegram chat
     */
    public function deletePost(Post $post): void
    {
        DeletePost::dispatch($this->telegram, $post);
    }

    /**
     * Delete message from Telegram chat at specific time
     */
    public function deletePostLater(Post $post, Carbon $deleteAt): void
    {
        DeletePost::dispatch($this->telegram, $post)->delay($deleteAt);
    }
}