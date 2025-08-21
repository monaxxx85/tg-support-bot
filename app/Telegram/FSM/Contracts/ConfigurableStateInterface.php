<?php

namespace App\Telegram\FSM\Contracts;


interface ConfigurableStateInterface
{
    public function configure(string $scenario, string $state): void;
}
