<?php

namespace App\Telegram\FSM\Abstract;

use App\Telegram\FSM\Contracts\ConfigurableStateInterface;
use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\Context;
use App\Telegram\FSM\Core\Event;
use App\Telegram\FSM\Core\EventBus;
use App\Telegram\FSM\Core\StateId;
use App\Telegram\FSM\Core\Registry;
use App\Telegram\FSM\Enum\EventType;
use App\Telegram\FSM\Enum\StateStep;

abstract class AbstractState implements StateInterface, ConfigurableStateInterface
{
    protected string $scenario;
    protected string $state;

    public function __construct(?string $scenario = null, ?string $state = null)
    {
        $this->scenario = $scenario ?? '';
        $this->state = $state ?? '';
    }

    public function configure(string $scenario, string $state): void
    {
        $this->scenario = $scenario;
        $this->state = $state;
    }

    public function id(): StateId
    {
        return new StateId($this->scenario, $this->state);
    }

    public function handle(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return match ($event->type()) {
            EventType::Message => $this->handleChatMessage($event, $context, $eventBus),
            EventType::Command => $this->handleCommand($event, $context, $eventBus),
            EventType::Callback => $this->handleCallbackQuery($event, $context, $eventBus),
            EventType::Update => $this->handleBotChatStatusUpdate($event, $context, $eventBus),
            EventType::Async => $this->handleAsyncEvent($event, $context, $eventBus), // <-- Важно
            EventType::Chain => $this->handleChainEvent($event, $context, $eventBus),
            default => $this->onFailure($event, $context, $eventBus)
        };
    }

    protected function next(string $nextState): StateId
    {
        return new StateId($this->scenario, $nextState);
    }

    protected function nextInOrder(): ?StateId
    {
        $registry = app(Registry::class);
        return $registry->getNextState($this->id());
    }

    protected function finish(): ?StateId
    {
        return $this->next(StateStep::Finished->value());
    }

    public function onEnter(Context $context): void
    {
        // Метод, вызываемый при входе в состояние (опциональный)
    }

    // Обновленные сигнатуры конкретных обработчиков
    protected function handleChatMessage(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return null;
    }

    protected function handleCommand(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return null;
    }

    protected function handleCallbackQuery(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return null;
    }

    protected function handleBotChatStatusUpdate(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return null;
    }

    protected function handleAsyncEvent(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        // Логируем необработанные асинхронные события
        \Log::debug("Unhandled Async event in state", [
            'state' => $this->id()->key(),
            'event_data' => $event->data
        ]);
        // Если состояние ожидает асинхронную задачу, очищаем флаг
        if ($context->isAsyncPending) {
            $context->clearAsyncPending();
        }
        return null;
    }

    protected function handleChainEvent(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        \Log::debug("Chain event received in state", [
            'state' => $this->id()->key(),
            'event_data' => $event->data
        ]);
        return null;
    }

    protected function onFailure(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        \Log::warning("Unhandled event type in state", [
            'state' => $this->id()->key(),
            'event_type' => $event->type()->value,
            'event_data' => $event->data
        ]);
        return null;
    }

}
