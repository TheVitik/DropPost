<?php

namespace App\Services\Unsplash;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class PhotoStore
{
    public function search(string $query): string
    {
        $search = Unsplash::search()
            ->term($query)
            ->orientation('squarish')
            ->toJson();
        $imageUrl = $search['results'][0]['urls']['regular'];

        return $imageUrl;
    }

    public function getPhoto(string $query): ?string
    {
        // add cache
        if (cache()->has('unsplash_' . md5($query))) {
            return cache()->get('unsplash_' . md5($query));
        }

        Log::info('Unsplash request', ['query' => $query]);

        $accessKey = env('UNSPLASH_ACCESS_KEY');
        $url = 'https://api.unsplash.com/search/photos?query=' . urlencode($query);

        $response = Http::withHeaders([
            'Accept-Version' => 'v1',
            'Authorization' => 'Client-ID ' . $accessKey,
        ])->get($url);

        Log::info('Unsplash response', ['response' => $response->json()]);

        $images = $response->json()['results'];

        if (empty($images)) {
            return null;
        }

        cache()->put('unsplash_' . md5($query), $images[0]['urls']['regular'], now()->addMinutes(5));

        return $images[0]['urls']['regular'];
    }
}