<?php

namespace App\Telegram\FSM\Contracts;

use App\Telegram\FSM\Core\Context;
use App\Telegram\FSM\Core\StateId;

interface ContextRepositoryInterface
{
    public function load(int $userId): ?Context;
    public function save(Context $ctx): void;
    public function setState(Context $ctx, StateId $state): void;
    public function reset(int $userId): void;
}
