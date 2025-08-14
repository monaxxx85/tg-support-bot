<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\FSM\Contracts\{ContextRepositoryInterface, ScenarioInterface};
use App\Telegram\FSM\Core\Event;

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

    public function start(string $scenario, int $userId,int $chatId, array $seed = [], ?int $ttlMinutes = null): Context
    {
        $sc = $this->scenarios[$scenario] ?? null;
        if (!$sc) throw new \RuntimeException("Unknown scenario: $scenario");

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
        $ctx = $this->contextRepository->load($userId);
        if ($ctx->state === null) {
            return false;
        }

        if($ctx->chatId !== $chatId){
            return false;
        }

        if (!$ctx->isActive()) {
            $this->contextRepository->reset($ctx->userId);
            return false;
        }

        $state = $this->registry->get($ctx->state);
        if (!$state) return false;


        $next = $state->handle($event, $ctx);
        $this->contextRepository->save($ctx);

        if ($next) {
            $this->contextRepository->setState($ctx, $next);
            $this->registry->get($next)?->onEnter($ctx);
        }

        return true;
    }
}

