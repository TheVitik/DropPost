<?php

namespace App\Services\Telegram\Client;

use danog\MadelineProto\API;

class PhotoService
{
    public function __construct(private API $client)
    {

    }

    public function getPhotoUrl(): string
    {
        // Get the user profile photo
        return '';
    }

}