<?php

namespace App\Telegram\FSM\Core;

use Illuminate\Support\Facades\Event as LaravelEvent;
use App\Telegram\FSM\Jobs\ProcessFSMJob;

class EventBus
{
    private array $eventQueue = [];
    private int $userId;
    private int $chatId;

    public function __construct(int $userId, int $chatId)
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
    }

    public function dispatch(string $eventName, array $data = []): void
    {
        $this->eventQueue[] = [
            'name' => $eventName,
            'data' => $data,
            'timestamp' => now()
        ];

        // Отправляем событие в Laravel Event System
        LaravelEvent::dispatch("fsm.{$eventName}", [
            'user_id' => $this->userId,
            'chat_id' => $this->chatId,
            'data' => $data
        ]);
    }

    public function dispatchAsyncJob(object $job): void
    {
        dispatch($job);
    }

    public function dispatchFSMEvent(Event $event): void
    {
        ProcessFSMJob::dispatch($this->userId, $this->chatId, $event);
    }

    public function processQueue(): array
    {
        $processed = $this->eventQueue;
        $this->eventQueue = [];
        return $processed;
    }

    public function hasEvents(): bool
    {
        return !empty($this->eventQueue);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }
}
