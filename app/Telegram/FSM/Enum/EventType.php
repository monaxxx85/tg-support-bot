<?php

namespace App\Telegram\FSM\Enum;

enum EventType: string
{
    case Message = 'text';
    case Command = 'command';
    case Callback = 'callback';
    case Update = 'update';
    case Finished = 'finished';
    case Async = 'async';
    case Chain = 'chain';


    public function value(): string
    {
        return $this->value;
    }
}
