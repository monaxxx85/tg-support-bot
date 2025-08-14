<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Core\StateId;

final class GetContactScenario implements ScenarioInterface
{
    public function __construct(
        protected readonly TelegramClientInterface    $telegramClient,
        protected readonly ContextRepositoryInterface $contextRepository,
        protected readonly SessionRepositoryInterface $sessionRepository,
    )
    {
    }

    public function name(): string
    {
        return 'get_contact';
    }

    public function initial(): StateId
    {
        return new StateId($this->name(), 'start');
    }

    public function registerStates(): array
    {
        return [
            new StartState($this->telegramClient),
            new FinishedState($this->telegramClient, $this->contextRepository, $this->sessionRepository),
        ];
    }
}
