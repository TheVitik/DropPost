<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GPTBot
{
    const API_URL = 'https://api.openai.com/v1/chat/completions';
    const CACHE_TTL = 5;

    protected int $maxTokens = 400;

    protected string $model = '';

    public function __construct(private string $apiKey)
    {
    }

    public function sendMessage(array $messages): string
    {
        // cache message for 5 minutes
        $cacheKey = 'openai_message_' . md5(serialize($messages));
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        Log::channel('openai')->info('OpenAI request', [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post(self::API_URL, [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
        ]);

        Log::channel('openai')->info('OpenAI response', ['response' => $response->json()]);

        if ($response->failed()) {
            Log::channel('openai')->error('OpenAI request failed', ['response' => $response->json()]);
            throw new \Exception('OpenAI request failed');
        }

        $text = $response->json()['choices'][0]['message']['content'];

        cache()->put($cacheKey, $text, now()->addMinutes(self::CACHE_TTL));

        return $text;
    }
}