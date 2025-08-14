<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Contracts\StateInterface;

final class Registry
{
    /** @var array<string, StateInterface> key = scenario.state */
    private array $states = [];

    public function registerScenario(ScenarioInterface $scenario): void
    {
        foreach ($scenario->registerStates() as $state) {
            $this->states[$state->id()->key()] = $state;
        }
    }

    public function get(StateId $id): ?StateInterface
    {
        return $this->states[$id->key()] ?? null;
    }
}

