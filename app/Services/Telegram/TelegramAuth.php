<?php

namespace App\Services\Telegram;

use App\Exceptions\NotTelegramException;
use App\Exceptions\TelegramDataOutdatedException;

class TelegramAuth
{
    public function __construct(private string $botToken)
    {
    }

    /**
     * @throws NotTelegramException
     * @throws TelegramDataOutdatedException
     */
    public function checkAuthorization(array $data): array
    {
        $check_hash = $data['hash'];
        unset($data['hash']);
        $data_check_arr = [];
        foreach ($data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->botToken, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new NotTelegramException();
        }
        if ((time() - $data['auth_date']) > 86400) {
            throw new TelegramDataOutdatedException();
        }
        return $data;
    }
}