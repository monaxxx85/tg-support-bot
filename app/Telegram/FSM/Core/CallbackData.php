<?php

namespace App\Telegram\FSM\Core;

final class CallbackData
{

    /**
     * @param string $scenario
     * @param string $state
     * @param string $event
     * @param array $params
     * @return string (fsm:registration;state:awaiting_email;event:confirm;uid:123)
     */
    public static function make(string $scenario, string $state, string $event, array $params = []): string
    {
        $base = "fsm:$scenario;state:$state;event:$event";
        foreach ($params as $k => $v) $base .= ";$k:$v";
        return $base;
    }

    public static function parse(string $data): array
    {
        $parts = explode(';', $data);
        $out = [];
        foreach ($parts as $p) {
            if (!$p) continue;
            [$k,$v] = array_pad(explode(':', $p, 2), 2, null);
            $out[$k] = $v;
        }
        return $out;
    }
}
