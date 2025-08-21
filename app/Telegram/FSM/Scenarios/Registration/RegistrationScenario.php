<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Abstract\AbstractScenario;
use App\Telegram\FSM\Enum\StateStep;

class RegistrationScenario extends AbstractScenario
{
    public function name(): string
    {
        return 'registration';
    }

    public function registerStates(): array
    {
        return [
            StateStep::Start->value() => StartState::class,
            'awaiting_name' => AwaitingNameState::class,
            'awaiting_email' => AwaitingEmailState::class,
            StateStep::Finished->value() => FinishedState::class
        ];
    }
}

