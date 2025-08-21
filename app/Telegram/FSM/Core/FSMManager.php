<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\Contracts\TelegramCallbackQuery;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUpdate;
use App\Telegram\FSM\Enum\EventType;

class FSMManager
{
    public function __construct(
        protected FSM $fsm
    ) {
    }

    public function processMessage(TelegramMessage $message): bool
    {
        return $this->fsm->dispatch(
            new Event(
                EventType::Message,
                ['text' => $message->text()]
            ),
            $message->from()->id(),
            $message->chat()->id()
        );
    }

    public function processCommand(TelegramMessage $message, string $command, ?string $parameter = null): bool
    {
        return $this->fsm->dispatch(
            new Event(
                EventType::Command,
                ['command' => $command, 'parameter' => $parameter]
            ),
            $message->from()->id(),
            $message->chat()->id()
        );
    }

    public function processCallback(TelegramCallbackQuery $callbackQuery): bool
    {
        return $this->fsm->dispatch(
            new Event(
                EventType::Callback,
                $callbackQuery->data()->toArray() ?? []
            ),
            $callbackQuery->from()->id(),
            $callbackQuery->message()->chat()->id()
        );
    }

    public function processStatusUpdate(TelegramUpdate $update): bool
    {
        return $this->fsm->dispatch(
            new Event(
                EventType::Update,
                ['update' => $update->toArray() ?? []]
            ),
            $update->from()->id(),
            $update->chat()->id()
        );
    }

    public function startScenario(string $scenario, int $userId, int $chatId, array $seed = [], ?int $ttlMinutes = null): void
    {
        $this->fsm->start($scenario, $userId, $chatId, $seed, $ttlMinutes);
    }

    public function resetScenario(int $userId): void
    {
        $this->fsm->reset($userId);
    }
}