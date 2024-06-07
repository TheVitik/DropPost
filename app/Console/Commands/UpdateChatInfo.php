<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Services\Telegram\TelegramChatService;
use Illuminate\Console\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;

class UpdateChatInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-chat-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Telegram channel info.';

    public function __construct(private TelegramChatService $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channels = Channel::all();

        foreach ($channels as $channel) {
            $this->info("Updating chat info for channel {$channel->id}");
            try {
                $chat = $this->service->getChat($channel->telegram_chat_id);
                $membersCount = $this->service->getMembersCount($channel->telegram_chat_id);
                $channel->update([
                    'name' => $chat->title,
                    'username' => $chat->username,
                    'description' => $chat->description,
                    'type' => $chat->type,
                    'members_count' => $membersCount
                ]);
            } catch (TelegramSDKException $e) {
                $this->error("Error getting chat info for channel {$channel->id}: {$e->getMessage()}");
                continue;
            } catch (\Exception $e) {
                $this->error("Error updating chat info for channel {$channel->id}: {$e->getMessage()}");
                continue;
            }
        }
    }
}
