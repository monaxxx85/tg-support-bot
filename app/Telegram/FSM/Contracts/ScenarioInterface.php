<?php

namespace App\Telegram\FSM\Contracts;

use App\Telegram\FSM\Core\StateId;

interface ScenarioInterface
{
    /** Имя сценария: registration */
    public function name(): string;

    /** Стартовое состояние */
    public function initial(): StateId;

    /** Регистрирует свои состояния в реестр */
    public function registerStates(): array; // [StateInterface, ...]
}
