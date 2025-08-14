<?php

namespace App\Telegram\Commands\PrivateChat;


use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Formatters\ViewFormatter;
use App\Telegram\FSM\Core\FSM;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;

class RegCommand extends BaseCommand
{
    public function __construct(
        protected FSM $fsm
    )
    {
    }

    public function getName(): string
    {
        return 'reg';
    }

    public function getAliases(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return "Запуск сценария регистрации";
    }

    public function execute(Message $message, ?string $parameter = null): void
    {
        $this->fsm->start('registration', $message->from()->id(), ['utm' => 'from_start_command']);
    }
}
