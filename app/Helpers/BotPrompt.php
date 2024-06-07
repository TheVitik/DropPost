<?php

namespace App\Helpers;

use App\Models\AIBot;
use Illuminate\Support\Facades\Log;

class BotPrompt
{
    public static function prepareMessages(AIBot $bot): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Ти контент-мейкер для соціальної мережі Telegram. Напиши пост текстом у форматі HTML parse для Telegram. Дозволено лише ці HTML теги strong,i,u,s,blockquote. Максимум 1000 символів..'
            ]
        ];

        $keywords = implode(', ', $bot->keywords);

        $text = "Тематика: {$bot->topic}. Ключові слова: {$keywords}. {$bot->prompt}";

        $messages[] = [
            'role' => 'user',
            'content' => $text
        ];

        return $messages;
    }

    public static function textSubjectMessages(string $text): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'Напиши 1-3 словами про що цей текст, вкажи конкретно тему, про яку я маю знайти фото.'
            ],
            [
                'role' => 'user',
                'content' => $text
            ]
        ];
    }
}