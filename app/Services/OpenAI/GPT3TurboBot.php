<?php

namespace App\Services\OpenAI;

class GPT3TurboBot extends GPTBot
{
    protected int $maxTokens = 1000;
    protected string $model = 'gpt-3.5-turbo';

    public function __construct(private string $apiKey)
    {
        parent::__construct($apiKey);
    }
}