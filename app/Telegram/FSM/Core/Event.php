<?php

namespace App\Telegram\FSM\Core;

final class Event
{
    public function __construct(
        public readonly string $type,   // 'text' | 'callback' | 'command'
        public readonly array  $data = [] // btn:  ['text'=>..., 'callback'=>..., ...]
    ) {}
}
