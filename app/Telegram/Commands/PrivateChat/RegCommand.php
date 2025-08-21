<?php

namespace App\Telegram\Commands\PrivateChat;

use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\FSM\Core\FSMManager;

class RegCommand extends BaseCommand
{
    public function __construct(
        protected FSMManager $fSMManager
    ) {
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
        return "Запуск сценария тестовой регистрации";
    }

    public function execute(TelegramMessage $message, ?string $parameter = null): void
    {
        $this->fSMManager->startScenario(
            'registration',
            $message->from()->id(),
            $message->chat()->id(),
            ['utm_source' => 'telegram'],
            10);
    }
}
