<?php

namespace App\Providers;

use App\Telegram\Clients\TelegraphClient;
use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\SupportChatInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Repository\SupportChatSessionRepository;
use App\Telegram\Services\AsyncSupportChatService;
use App\Telegram\Resolvers\MessageTypeResolver;
use App\Telegram\Services\SupportChatService;
use Illuminate\Support\ServiceProvider;
use App\Telegram\Commands\CommandResolver;
use App\Telegram\Commands\CommandRouter;
use App\Telegram\DTO\TelegramConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramConfig::class, function () {
            return new TelegramConfig(supportGroupId: (int) config('telegraph.support_group_id'));
        });

        $this->app->bind(SupportChatInterface::class, function ($app) {
            return config('telegraph.use_queue')
                ? $app->make(AsyncSupportChatService::class)
                : $app->make(SupportChatService::class);
        });

        $this->app->singleton(TelegramClientInterface::class, function ($app) {
            $client = new TelegraphClient(config('telegraph.use_queue'));
            //            $client = new LoggingTelegramClient($client, 'support-bot');
            return $client;
        });


        $this->app->singleton(MessageTypeResolverInterface::class, MessageTypeResolver::class);
        $this->app->singleton(SessionRepositoryInterface::class, SupportChatSessionRepository::class);

        $this->app->singleton('commands.private', function ($app) {
            return new CommandResolver([
                $app->make(\App\Telegram\Commands\PrivateChat\StartCommand::class),
                // сюда добавляем приватные команды
            ]);

        });

        $this->app->singleton('commands.support', function ($app) {
            return new CommandResolver([
                $app->make(\App\Telegram\Commands\SupportChat\ContactCommand::class),
                // сюда добавляем команды для поддержки
            ]);

        });

        $this->app->singleton('commands.general', function ($app) {
            return new CommandResolver([
                $app->make(\App\Telegram\Commands\GeneralChat\TestCommand::class),
                // сюда добавляем приватные команды
            ]);

        });

        $this->app->singleton(CommandRouter::class, function ($app) {
            return new CommandRouter(
                $app->make(MessageTypeResolverInterface::class),
                $app->make('commands.private'),
                $app->make('commands.support'),
                $app->make('commands.general'),
            );
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
