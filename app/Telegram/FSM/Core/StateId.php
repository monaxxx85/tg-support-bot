<?php

namespace App\Telegram\FSM\Core;


final class StateId
{
    public function __construct(
        public readonly string $scenario,  // registration
        public readonly string $state      // awaiting_name
    ) {}

    public function key(): string
    {
        return $this->scenario.':'.$this->state;
    }

    public static function parse(string $full): self
    {
        [$scenario, $state] = explode(':', $full, 2);
        return new self($scenario, $state);
    }
}
