<?php

namespace App\Services\Telegram\Client;

use App\Exceptions\Telegram\PasswordRequiredException;
use App\Exceptions\Telegram\UnauthorizedException;
use danog\MadelineProto\API;
use danog\MadelineProto\Exception;

class AuthService
{
    private API $client;

    /**
     * @throws Exception
     */
    public function __construct(string $number)
    {
        $settings = (new \danog\MadelineProto\Settings\AppInfo)
            ->setApiId(config('telegram.api.id'))
            ->setApiHash(config('telegram.api.hash'));

        $this->client = new API($number, $settings);
    }

    /**
     * Login with phone
     */
    public function login(string $phone): void
    {
        $this->client->phoneLogin($phone);
    }

    /**
     * Confirm login with code
     *
     * @throws PasswordRequiredException
     * @throws UnauthorizedException
     */
    public function confirm(string $code): array
    {
        $this->client->completePhoneLogin($code);
        if ($this->client->getAuthorization() === API::WAITING_PASSWORD) {
            throw new PasswordRequiredException();
        }
        $user = $this->client->getSelf();
        if (!$user) {
            throw new UnauthorizedException();
        }

        return $user;
    }

    /**
     * Confirm login with password if 2FA is enabled
     * @throws UnauthorizedException
     */
    public function password(string $password): array
    {
        $this->client->complete2faLogin($password);

        $user = $this->client->getSelf();

        if (!$user) {
            throw new UnauthorizedException();
        }

        return $user;
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $this->client->logOut();
    }
}