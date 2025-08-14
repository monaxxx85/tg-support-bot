<?php

namespace App\Telegram\FSM\Core;

final class Context
{
    public function __construct(
        public int $userId,
        public ?StateId $state = null,
        public array $bag = [],
        public ?string $ttlAt = null
    ) {}

    // $bag произвольные данные сценария
    // $ttlAt = now()->addMinutes(10)->toIso8601String(); 10 минту сесисия сценария
}
