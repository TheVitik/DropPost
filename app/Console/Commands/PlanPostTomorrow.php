<?php

namespace App\Console\Commands;

use App\Jobs\AIBot\BotSendPost;
use App\Models\Channel;
use App\Services\ContentFormatter;
use App\Services\OpenAI\GPT3TurboBot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class PlanPostTomorrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:plan-post-tomorrow {channel?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule jobs for posting tomorrow.';

    public function __construct(
        private GPT3TurboBot $service,
        private Api $telegram,
        private ContentFormatter $formatter
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channelID = $this->argument('channel');

        // if channel ID is provided then process only that channel
        if ($channelID) {
            $channel = Channel::with('aiBot')
                ->where('id', $channelID)
                ->where('is_bot_active', true)
                ->first();
            if (!$channel) {
                $this->error("Channel with ID {$channelID} not found.");
                return;
            }
            $this->processChannel($channel);
        } else {
            $this->processAllChannels();
        }

        $this->info('Post planning is done.');
    }

    private function processAllChannels(): void
    {
        $channels = Channel::with('aiBot')->where('is_bot_active', true)->get();
        $channels->each(function (Channel $channel) {
            $this->info("Planning post for channel {$channel->id}...");
            $this->processChannel($channel);

            $this->info("Post planning is done for channel {$channel->id}.");
        });
    }

    private function processChannel(Channel $channel): void
    {
        $planning = $channel->aiBot->post_planning;
        // if empty then skip
        if (empty($planning)) {
            $this->info("Post planning is empty for channel {$channel->id}.");
            return;
        }
        $todayDay = now()->dayOfWeek;
        $tomorrowDay = $todayDay + 1;
        $tomorrowDay = $tomorrowDay > 6 ? 0 : $tomorrowDay;

        $tomorrow = now()->addDay();
        $tomorrow->setSecond(0);

        foreach ($planning as $plan) {
            if ($plan['day'] === $tomorrowDay) {
                sort($plan['time']);
                foreach ($plan['time'] as $time) {
                    $delay = $tomorrow->setTimeFromTimeString($time);
                    Log::info('COMMAND BotSendPost dispatch', ['delay' => $delay]);
                    BotSendPost::dispatch(
                        $channel->aiBot,
                        $channel,
                        $this->service,
                        $this->telegram,
                        $this->formatter
                    )->delay($delay);
                }
            }
        }
    }
}
