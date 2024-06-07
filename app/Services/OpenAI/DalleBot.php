<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DalleBot
{
    const API_URL = 'https://api.openai.com/v1/images/generations';
    const CACHE_TTL = 5;

    protected string $model = 'dall-e-3';
    protected string $imageSize = '1024x1024';
    protected int $count = 1;

    public function __construct(private string $apiKey)
    {
    }

    public function generatePhoto(string $prompt): string
    {
        // cache message for 5 minutes
        $cacheKey = 'openai_image_' . md5(serialize($prompt));
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        Log::channel('openai')->info('OpenAI request', [
            'model' => $this->model,
            'prompt' => $prompt,
            'n' => $this->count,
            'size' => $this->imageSize,
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post(self::API_URL, [
            'model' => $this->model,
            'prompt' => $prompt,
            'n' => $this->count,
            'size' => $this->imageSize,
        ]);

        Log::channel('openai')->info('OpenAI response', ['response' => $response->json()]);

        if ($response->failed()) {
            Log::channel('openai')->error('OpenAI request failed', ['response' => $response->json()]);
            throw new \Exception('OpenAI request failed');
        }

        $imageUrl = $response->json()['data'][0]['url'];

        cache()->put($cacheKey, $imageUrl, now()->addMinutes(self::CACHE_TTL));

        return $imageUrl;
    }
}