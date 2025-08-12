<?php

namespace App\Telegram\Commands\GeneralChat;


use App\Telegram\Contracts\TelegramClientInterface;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\DTO\TelegramConfig;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

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
        return "Тестовое сообщение  /test {parameter?}";
    }

    public function execute(Message $message, ?string $parameter = null): void
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
                    Keyboard::make()->buttons([
                        Button::make('Run')->action('test')->param('var1', '1')->param('var2', '2'),
                        Button::make('Open')->url('https://google.com'),
                        Button::make('Search')->switchInlineQuery('query')
                    ])->chunk(3)
                );
        }
    }
}
