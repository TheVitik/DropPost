<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Chat;

class TelegramChatService extends TelegramBase
{
    /**
     * @throws TelegramSDKException
     */
    public function getChat(string $chatId, bool $noCache = false): Chat
    {
        if (!$noCache && cache()->has($chatId)) {
            return cache()->get($chatId);
        }
        $chat = $this->telegram->getChat([
            'chat_id' => $chatId
        ]);
        cache()->put($chatId, $chat, 60 * 60 * 12);
        return $chat;
    }

    /**
     * @throws TelegramSDKException
     */
    public function getMembersCount(string $chatId, bool $noCache = false): int
    {
        if (!$noCache && cache()->has($chatId . '_members_count')) {
            return cache()->get($chatId . '_members_count');
        }
        $count = $this->telegram->getChatMemberCount([
            'chat_id' => $chatId
        ]);

        cache()->put($chatId . '_members_count', $count, 60 * 60 * 12);
        return $count;
    }

    /**
     * @throws TelegramSDKException
     */
    public function getPhoto(string $photoId): string
    {
        $file = $this->telegram->getFile([
            'file_id' => $photoId
        ]);

        $name = '/photos/' . $file->fileId . '.jpg';
        $path = storage_path('app/public' . $name);
        $this->telegram->downloadFile($file, $path);
        return $name;
    }
}