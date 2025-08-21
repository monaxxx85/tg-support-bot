<?php

namespace App\Telegram\DTO;

use App\Telegram\Contracts\TelegramUser;

class User implements TelegramUser
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $username = null,
        public readonly ?bool $isBot = false,
        public readonly ?string $firstName = '',
        public readonly ?string $lastName = '',
        public readonly ?string $languageCode = '',
        public readonly ?bool $isPremium = false,
    ) {}

    // Фабричный метод для создания из массива
    public static function fromArray(array $data): TelegramUser
    {
        return new self(
            id: $data['id'],
            username: $data['username'] ?? null,
            isBot: $data['is_bot'] ?? false,
            firstName: $data['first_name'] ?? '',
            lastName: $data['last_name'] ?? '',
            languageCode: $data['language_code'] ?? '',
            isPremium: $data['is_premium'] ?? false,
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function isBot(): bool
    {
        return $this->isBot ?? false;
    }

    public function firstName(): string
    {
        return $this->firstName ?? '';
    }

    public function lastName(): string
    {
        return $this->lastName ?? '';
    }

    public function languageCode(): string
    {
        return $this->languageCode ?? '';
    }

    public function isPremium(): bool
    {
        return $this->isPremium ?? false;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'is_bot' => $this->isBot,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'language_code' => $this->languageCode,
            'is_premium' => $this->isPremium,
        ];
    }
}