<?php

namespace App\Telegram\Commands\PrivateChat;


use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Formatters\ViewFormatter;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;

class StartCommand extends BaseCommand
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient
    ){}

    public function getName(): string
    {
        return 'start';
    }

    public function getAliases(): array
    {
        return ['начать', 'старт','go'];
    }

    public function getDescription(): string
    {
        return "Начало работы с ботом";
    }

    public function execute(Message $message, ?string $parameter = null): void
    {

        $this->telegramClient->sendMessage(
            $message->chat()->id(),
            (new ViewFormatter)->render(null,'telegram.start')
        );
    }
}
