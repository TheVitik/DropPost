<?php

namespace App\Services;

use App\Jobs\AIBot\BotSendPost;
use App\Models\AIBot;
use App\Models\Channel;
use App\Services\OpenAI\GPT3TurboBot;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class BotPostPlanner
{
    public function __construct(
        private GPT3TurboBot $service,
        private Api $telegram,
        private ContentFormatter $formatter
    ) {
    }

    public function plan(AIBot $bot, Channel $channel): void
    {
        $planning = $bot->post_planning;
        if (empty($planning)) {
            throw new \Exception('Post planning is empty');
        }

        $now = now();
        $now->setSecond(0);
        $currentDay = $now->dayOfWeek;
        $currentHour = $now->format('H:i');

        foreach ($planning as $plan) {
            if ($plan['day'] == $currentDay) {
                sort($plan['time']);
                Log::info('BotSendPost dispatch', ['plan' => $plan]);
                foreach ($plan['time'] as $time) {
                    Log::info('BotSendPost dispatch', ['time' => $time]);
                    if ($time > $currentHour) {
                        $delay = $now->setTimeFromTimeString($time);
                        Log::info('BotSendPost dispatch', ['delay' => $delay]);
                        BotSendPost::dispatch($bot, $channel, $this->service, $this->telegram, $this->formatter)->delay(
                            $delay
                        );
                    }
                }
                break;
            }
        }

        // if current time > 23:00, schedule for tomorrow
        if ($currentHour > '23:00') {
            // call command app:plan-post-tomorrow {channel}
            $command = "app:plan-post-tomorrow $channel->id";
            Artisan::call($command);
        }
    }

    public function planOld(AIBot $bot, Channel $channel)
    {
        $planning = $bot->post_planning;
        if (empty($planning)) {
            throw new \Exception('Post planning is empty');
        }

        $now = now();
        $now->setSecond(0);
        $currentDay = $now->dayOfWeek;
        $currentHour = $now->format('H:i');

        // Week days after current day including current day
        for ($i = $currentDay; $i < 7; $i++) {
            if (!isset($planning[$i])) {
                continue;
            }
            foreach ($planning[$i] as $time) {
                if ($i == $currentDay) {
                    if ($time < $currentHour) {
                        continue;
                    } else {
                        $delay = $now->setTimeFromTimeString($time);
                        $diffInSeconds = $delay->diffInSeconds(now());
                        BotSendPost::dispatch($bot, $channel, $this->service, $this->telegram, $this->formatter)->delay(
                            $delay
                        );
                        return;
                    }
                }

                $delay = $now->addDays($this->getDiffDays($i))->setTimeFromTimeString($time);
                $diffInSeconds = $delay->diffInSeconds(now());
                BotSendPost::dispatch($bot, $channel, $this->service, $this->telegram, $this->formatter)->delay($delay);
                return;
            }
        }

        // Week days before current day
        for ($i = 0; $i < $currentDay; $i++) {
            if (!isset($planning[$i])) {
                continue;
            }
            foreach ($planning[$i] as $time) {
                $delay = $now->addDays($this->getDiffDays($i))->setTimeFromTimeString($time);
                $diffInSeconds = $delay->diffInSeconds(now());
                BotSendPost::dispatch($bot, $channel, $this->service, $this->telegram, $this->formatter)->delay($delay);
                return;
            }
        }

        // If no post was planned other than current day before current time
        $delay = $now->addDays($this->getDiffDays($currentDay))->setTimeFromTimeString($planning[$currentDay][0]);
        $diffInSeconds = $delay->diffInSeconds(now());
        BotSendPost::dispatch($bot, $channel, $this->service, $this->telegram, $this->formatter)->delay($delay);
    }

    private function getDiffDays(int $day): int
    {
        $now = now();
        $currentDay = $now->dayOfWeek;
        if ($day > $currentDay) {
            return $day - $currentDay;
        }
        return 7 - $currentDay + $day;
    }

}