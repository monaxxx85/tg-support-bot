<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Enum\EventType;
use App\Telegram\FSM\Enum\StateStep;
use App\Telegram\FSM\Jobs\ProcessFSMJob;

final class FSM
{
    public function __construct(
        private readonly Registry                   $registry,
        private readonly ContextRepositoryInterface $contextRepository,
        /** @var array<string, ScenarioInterface> */
        private readonly array                      $scenarios = []
    )
    {
    }

    public function start(string $scenario, int $userId, int $chatId, array $seed = [], ?int $ttlMinutes = null): Context
    {
        $sc = $this->scenarios[$scenario] ?? null;
        if (!$sc) {
            throw new \RuntimeException("Unknown scenario: $scenario");
        }

        $ctx = new Context(
            $userId,
            $chatId,
            $sc->initial(),
            $seed,
            $ttlMinutes ? now()->addMinutes($ttlMinutes)->toIso8601String() : null
        );

        $this->contextRepository->save($ctx);

        $state = $this->registry->get($ctx->state);
        $state?->onEnter($ctx);

        return $ctx;
    }

    public function dispatch(Event $event, int $userId, int $chatId): bool
    {
        $ctx = $this->getContext($userId);

        // Нет контекста - ничего не делаем
        if (!$ctx || $ctx->state === null) {
            return false;
        }

        // Проверка чата
        if ($ctx->chatId !== $chatId) {
            return false;
        }

        // Проверка асинхронных операций
        if ($ctx->isAsyncPending) {
            if ($event->type() !== EventType::Async &&
                $event->type() !== EventType::Chain &&
                ($event->data['async_job_id'] ?? null) !== $ctx->asyncJobId) {

                \Log::debug('FSM: Ignoring event while async operation pending', [
                    'user_id' => $userId,
                    'event_type' => $event->type()->value(),
                    'async_job_id' => $ctx->asyncJobId
                ]);
                return true;
            }
        }

        // Получаем текущее состояние
        $currentState = $this->registry->get($ctx->state);

        if (!$currentState) {
            \Log::warning("State not found in registry", ['state_id' => $ctx->state->key()]);
            $this->reset($userId);
            return false;
        }

        $eventBus = new EventBus($userId, $chatId);

        // Обрабатываем событие текущим состоянием
        $nextStateId = $currentState->handle($event, $ctx, $eventBus);

        // Сохраняем изменения в контексте
        $this->contextRepository->save($ctx);

        // Обрабатываем цепочку событий из EventBus
        if ($eventBus->hasEvents()) {
            $this->processEventBus($eventBus, $ctx);
        }

        // Если нет перехода, останавливаемся
        if (!$nextStateId) {
            return true;
        }

        // --- Логика перехода в новое состояние ---
        $ctx->state = $nextStateId;
        $isFinished = ($nextStateId->state === StateStep::Finished->value());

        // Если состояние не завершено, сохраняем переход в контексте
        if (!$isFinished) {
            $this->contextRepository->save($ctx);
        }

        // Получаем новое состояние
        $nextState = $this->registry->get($nextStateId);
        if (!$nextState) {
            \Log::warning("Next state not found in registry", ['state_id' => $nextStateId->key()]);

            if ($isFinished) {
                $this->reset($userId);
            }

            return false;
        }

        // Вызываем onEnter для нового состояния
        try {
            $nextState->onEnter($ctx);
        } catch (\Throwable $e) {
            \Log::error("Error in state onEnter", [
                'state_id' => $nextStateId->key(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

        }

        if ($isFinished) {
            $this->reset($userId);
        }

        return true;
    }

    private function processEventBus(EventBus $eventBus, Context $ctx): void
    {
        $events = $eventBus->processQueue();
        foreach ($events as $eventData) {
            $chainEvent = new Event(
                EventType::Chain,
                ['origin_event' => $eventData, 'source_state' => $ctx->state->key() ?? 'unknown']
            );
            ProcessFSMJob::dispatch($eventBus->getUserId(), $eventBus->getChatId(), $chainEvent);
        }
    }

    public function reset(int $userId): void
    {
        $this->contextRepository->reset($userId);
    }

    public function isActive(int $userId): bool
    {
        $ctx = $this->contextRepository->load($userId);
        return $ctx && $ctx->state !== null && !$ctx->isExpired();
    }

    public function getContext(int $userId): ?Context
    {
        $ctx = $this->contextRepository->load($userId);
        if ($ctx && $ctx->isExpired()) {
            $this->contextRepository->reset($userId);
            return null;
        }
        return $ctx;
    }
}
