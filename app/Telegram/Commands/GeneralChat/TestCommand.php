<?php

namespace App\Telegram\Commands\GeneralChat;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\DTO\TelegramConfig;

class TestCommand extends BaseCommand
{
    protected int $supportChatId;

    public function __construct(
        protected TelegramClientInterface $telegramClient,
        protected TelegramConfig $config,
        protected SessionRepositoryInterface $sessionRepository,
    ) {
        $this->supportChatId = $config->supportGroupId;
    }

    public function getName(): string
    {
        return 'test';
    }

    public function getAliases(): array
    {
        return ['тест'];
    }

    public function getDescription(): string
    {
        return "Тестовое сообщение для демострации кнопок /test {parameter?}";
    }

    public function execute(TelegramMessage $message, ?string $parameter = null): void
    {

        if ($parameter === "") {

            $this->telegramClient
                ->sendMessage(
                    $this->supportChatId,
                    "Тестовое сообщения с кнопками ",
                    null,
                    [
                        ['text' => 'Run', 'callback_data' => 'action:test;var1:1;var2:2'],
                        ['text' => 'Open', 'url' => 'https://google.com'],
                    ]
                );


        } else {

            $this->telegramClient
                ->sendMessage(
                    $this->supportChatId,
                    "Тестовое сообщения с кнопками 2",
                    null,
                    [
                        [
                            ['text' => 'Run', 'callback_data' => 'action:test;var1:1;var2:2'],
                            ['text' => 'Open', 'url' => 'https://google.com'],

                        ],
                        [
                            ['text' => 'Search', 'switch_inline_query' => 'query'],
                        ]
                    ]
                );
        }
    }
}
