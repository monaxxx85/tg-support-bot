<?php

namespace App\Telegram\FSM\Repository;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Core\Context;
use App\Telegram\FSM\Core\StateId;

class ContextRepository implements ContextRepositoryInterface
{

    public function __construct(
        protected SessionRepositoryInterface $sessionRepository
    ){}

    public function load(int $userId): ?Context
    {
        $session = $this->sessionRepository->findByUser($userId);
        if (!$session) {
            return null;
        }

        // Проверяем обязательные поля
        if (!isset($session->telegram_user_id)) {
            \Log::info('Missing telegram_user_id in session', ['user_id' => $userId]);
            return null;
        }

        return new Context(
            (int)$session->telegram_user_id,
            $session->chat_id_fsm ? (int)$session->chat_id_fsm : null,
            $session->state_fsm ? StateId::parse($session->state_fsm) : null,
            is_array($session->bag_fsm) ? $session->bag_fsm : [],
            $session->ttl_at_fsm ?? null,
            $session->is_async_pending_fsm ?? false,
            $session->async_job_id_fsm ?? null,
        );

    }

    public function save(Context $ctx): void
    {
        $session = $this->sessionRepository->findByUser($ctx->userId);
        if (!$session) {
            return;
        }

        $session->state_fsm = $ctx->state?->key();
        $session->chat_id_fsm = $ctx->chatId;
        $session->bag_fsm = $ctx->bag;
        $session->ttl_at_fsm = $ctx->ttlAt;
        $session->is_async_pending_fsm = $ctx->isAsyncPending;
        $session->async_job_id_fsm = $ctx->asyncJobId;

        $this->sessionRepository->saveSession($session);
    }

    public function setState(Context $ctx, StateId $state): void
    {
        $ctx->state = $state;
        $this->save($ctx);
    }

    public function reset(int $userId): void
    {
        $session = $this->sessionRepository->findByUser($userId);
        if (!$session) {
            return;
        }

        $session->state_fsm = null;
        $session->chat_id_fsm = null;
        $session->bag_fsm = [];
        $session->ttl_at_fsm = null;
        $session->is_async_pending_fsm = false;
        $session->async_job_id_fsm = null;

        $this->sessionRepository->saveSession($session);
    }
}
