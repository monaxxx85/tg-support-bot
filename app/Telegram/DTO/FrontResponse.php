<?php

namespace App\Telegram\DTO;

class FrontResponse
{
    public function __construct(
        public bool $continue,
        public mixed $payload = null
    ) {}

    public static function stop(mixed $payload = null): self
    {
        return new self(false, $payload);
    }

    public static function next(mixed $payload = null): self
    {
        return new self(true, $payload);
    }
}
