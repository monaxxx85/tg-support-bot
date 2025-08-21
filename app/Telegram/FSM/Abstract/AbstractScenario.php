<?php

namespace App\Telegram\FSM\Abstract;

use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Core\StateId;

abstract class AbstractScenario implements ScenarioInterface
{
    abstract public function name(): string;

    abstract public function registerStates(): array;

    public function initial(): StateId
    {
        return $this->createStateId('start');
    }

    /**
     * Утилита для удобного создания StateId внутри сценария
     */
    protected function createStateId(string $state): StateId
    {
        return new StateId($this->name(), $state);
    }
}