<?php

namespace App\Providers;

use App\Services\OpenAI\GPTBot;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Guard $auth): void
    {
        $settings = (new \danog\MadelineProto\Settings\AppInfo)
            ->setApiId(config('telegram.api.id'))
            ->setApiHash(config('telegram.api.hash'));

        /*$this->app->singleton(
            \danog\MadelineProto\API::class,
            function ($app) use ($settings) {
                if (request()->has('phone')) {
                    return new \danog\MadelineProto\API(request()->get('phone'), $settings);
                }
                if (auth()->user()) {
                    return new \danog\MadelineProto\API(auth()->user()->phone, $settings);
                }
                //return new \danog\MadelineProto\API('session.madeline', $settings);

                return null;
            }
        );*/

        // Bind TelegramAuth pass config telegram bot token
        $this->app->bind(\App\Services\Telegram\TelegramAuth::class, function ($app) {
            return new \App\Services\Telegram\TelegramAuth(config('telegram.bots.droppost.token'));
        });

        // Bing GPTBot pass config openai api secret if need GPT3TurboBot
        $this->app->bind(\App\Services\OpenAI\GPT3TurboBot::class, function ($app) {
            return new \App\Services\OpenAI\GPT3TurboBot(env('OPENAI_API_SECRET'));
        });
    }
}
