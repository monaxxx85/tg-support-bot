<?php

namespace App\Telegram\FSM\Abstract;

use App\Telegram\FSM\Core\Context;
use App\Telegram\FSM\Core\Event;
use App\Telegram\FSM\Core\StateId;
use App\Telegram\FSM\Jobs\ProcessFSMJob;
use App\Telegram\FSM\Enum\EventType;

abstract class AsyncState extends AbstractState
{
    protected function dispatchAsyncJob(object $job): string
    {
        $jobId = uniqid('async_', true);

        // Добавляем информацию о контексте в job если нужно
        if (method_exists($job, 'setContextInfo')) {
            $job->setContextInfo($this->scenario, $this->state);
        }

        // Отправляем job в очередь
        dispatch($job);

        return $jobId;
    }

    protected function waitForAsync(Context $context, string $jobId): void
    {
        $context->setAsyncPending($jobId);
    }

    protected function completeAsync(Context $context): ?StateId
    {
        $context->clearAsyncPending();
        return $this->nextInOrder();
    }

    // Метод для отправки асинхронного события обратно в FSM
    protected function dispatchAsyncEvent(Context $context, array $data = []): void
    {
        $event = new Event(EventType::Async, array_merge($data, [
            'async_job_id' => $context->asyncJobId
        ]));

        ProcessFSMJob::dispatch($context->userId, $context->chatId, $event);
    }
}
