<?php

namespace App\Telegram\FSM\Enum;

enum StateStep: string
{
    case Start = 'start';
    case Finished = 'finished';

    public function value(): string
    {
        return $this->value;
    }
}
