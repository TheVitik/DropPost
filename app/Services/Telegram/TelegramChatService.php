<?php

namespace App\Services\Telegram;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Chat;

class TelegramChatService extends TelegramBase
{
    /**
     * @throws TelegramSDKException
     */
    public function getChat(string $chatId): Chat
    {
        return $this->telegram->getChat([
            'chat_id' => $chatId
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    public function getMembersCount(string $chatId): int
    {
        return $this->telegram->getChatMemberCount([
            'chat_id' => $chatId
        ]);
    }
}