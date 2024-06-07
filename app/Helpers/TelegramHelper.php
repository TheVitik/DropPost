<?php

namespace App\Helpers;

class TelegramHelper
{
    const TYPE_CHAT_ID = 'chat_id';
    const TYPE_USERNAME = 'username';

    // determine is chat id or username
    public static function determineType(string $username): string
    {
        return is_numeric($username) ? self::TYPE_CHAT_ID : self::TYPE_USERNAME;
    }
}