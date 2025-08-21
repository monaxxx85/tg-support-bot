<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\FSM\Enum\EventType;

final class Event
{
    public function __construct(
        public readonly EventType $type,
        public readonly array  $data = [] // btn:  ['text'=>..., 'callback'=>..., ...]
    ) {}

    public function name(): string
    {
        return $this->data['event'] ?? '';
    }  
    
    public function type(): EventType
    {
        return $this->type;
    }
}
