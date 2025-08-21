<?php

namespace App\Telegram\Adapters\Arrays;

use App\Telegram\Contracts\TelegramUser;

class ArrayTelegramUser implements TelegramUser
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function id(): int
    {
        return $this->data['id'] ?? 0;
    }

    public function username(): ?string
    {
        return $this->data['username'] ?? null;
    }

    public function isBot(): bool
    {
        return $this->data['is_bot'] ?? false;
    }

    public function firstName(): string
    {
        return $this->data['first_name'] ?? '';
    }

    public function lastName(): string
    {
        return $this->data['last_name'] ?? '';
    }

    public function languageCode(): string
    {
        return $this->data['language_code'] ?? '';
    }

    public function isPremium(): bool
    {
        return $this->data['is_premium'] ?? false;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}