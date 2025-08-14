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

    public function start(string $scenario, int $userId, array $seed = []): Context
    {
        $sc = $this->scenarios[$scenario] ?? null;
        if (!$sc) throw new \RuntimeException("Unknown scenario: $scenario");

        $ctx = new Context($userId, $sc->initial(), $seed);
        $this->contextRepository->save($ctx);

        $state = $this->registry->get($ctx->state);
        $state?->onEnter($ctx);
        return $ctx;
    }

    public function dispatch(Event $event, int $userId): bool
    {
        $ctx = $this->contextRepository->load($userId) ?? new Context($userId);
        if (!$ctx->state) {
            // нет активного сценария — можно игнорить или стартовать дефолтный
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

