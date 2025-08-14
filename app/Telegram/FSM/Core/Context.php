<?php

namespace App\Telegram\FSM\Core;

use Carbon\Carbon;

final class Context
{
    public function __construct(
        public int      $userId,
        public int      $chatId,
        public ?StateId $state = null,
        public array    $bag = [],
        public ?string  $ttlAt = null
    ){}

    public function isActive(): bool
    {
        if ($this->ttlAt === null) {
            return true;
        }

        return Carbon::now()->lt(Carbon::parse($this->ttlAt));
    }

}
