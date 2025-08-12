<?php

namespace App\Telegram\DTO;


class TelegramConfig
{
    public function __construct(
        public readonly int $supportGroupId
    ) {}
}
