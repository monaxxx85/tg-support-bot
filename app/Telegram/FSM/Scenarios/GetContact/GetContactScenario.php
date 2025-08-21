<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

use App\Telegram\FSM\Abstract\AbstractScenario;
use App\Telegram\FSM\Enum\StateStep;

final class GetContactScenario extends AbstractScenario
{

    public function name(): string
    {
        return 'get_contact';
    }


    public function registerStates(): array
    {
        return [
            StateStep::Start->value() => StartState::class,
            StateStep::Finished->value() => FinishedState::class,
        ];
    }
}
