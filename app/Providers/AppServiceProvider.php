<?php

namespace App\Providers;

use App\Telegram\Clients\LoggingTelegramClient;
use App\Telegram\Clients\TelegraphClient;
use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Handlers\Handler;
use App\Telegram\Repository\SupportChatSessionRepository;
use App\Telegram\Services\MessageTypeResolver;
use App\Telegram\Services\SupportChatService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramClientInterface::class, function ($app) {
            $client = new TelegraphClient();
            $client = new LoggingTelegramClient($client, 'support-bot');
            return $client;
        });


        $this->app->singleton(MessageTypeResolverInterface::class, function ($app) {
            return new MessageTypeResolver(
                config('telegraph.support_group_id')
            );
        });

        $this->app->singleton(SessionRepositoryInterface::class,SupportChatSessionRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
