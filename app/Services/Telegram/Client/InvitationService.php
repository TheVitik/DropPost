<?php

namespace App\Services\Telegram\Client;

use App\Models\Invitation;
use App\Models\Project;
use danog\MadelineProto\API;
use danog\MadelineProto\Exception;
use danog\MadelineProto\ParseMode;

class InvitationService
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

    public function inviteByUsername(string $username, Project $project, Invitation $invitation): void
    {
        $link = route('invitation.show', $invitation->id);
        $this->client->sendMessage(
            $username,
            "Я запрошую тебе приєднатися до проекту $project->name.<br>Для цього перейди за посиланням: <a href=\"$link\">Приєднатися</a>",
            ParseMode::HTML
        );
    }
}