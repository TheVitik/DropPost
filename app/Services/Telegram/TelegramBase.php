<?php

namespace App\Services\Telegram;

use Telegram\Bot\Api;

class TelegramBase
{
    public function __construct(protected Api $telegram)
    {
    }
}