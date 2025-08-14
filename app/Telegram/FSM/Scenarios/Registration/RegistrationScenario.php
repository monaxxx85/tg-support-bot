<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Core\StateId;

final class RegistrationScenario implements ScenarioInterface
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
        protected readonly ContextRepositoryInterface $contextRepository,
    ){}

    public function name(): string
    {
        return 'registration';
    }

    public function initial(): StateId
    {
        return new StateId($this->name(), 'start');
    }

    public function registerStates(): array
    {
        return [
            new StartState($this->telegramClient),
            new AwaitingNameState($this->telegramClient),
            new AwaitingEmailState($this->telegramClient),
            new FinishedState($this->telegramClient,$this->contextRepository),
        ];
    }
}
