<?php

namespace App\Telegram\DTO;

use Carbon\Carbon;
use App\Telegram\Enum\ChatStatus;
use DefStudio\Telegraph\DTO\User;

class ChatSession
{
    public function __construct(
        public ChatStatus $status,
        public readonly int $telegram_user_id,
        public int $topicId,
        public string $firstName,
        public string $lastName,
        public ?string $username,
        public bool $isBot,
        public string $languageCode,
        public bool $isPremium,
        public string $created_at,
        public ?string $closed_at = null,
        public ?string $closed_reason = null,
        public bool $is_banned = false,
        public string $last_message_from = 'user', // 'user' | 'support'
    ) {
    }

    /**
     * Проверяет, активна ли сессия
     */
    public function isActive(): bool
    {
        return $this->closed_at === null && !$this->is_banned;
    }

    /**
     * Проверяет, заблокирован ли пользователь
     */
    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    /**
     * Возвращает время жизни сессии в минутах
     */
    public function durationInMinutes(): int
    {
        return Carbon::parse($this->created_at)
            ->diffInMinutes(Carbon::parse($this->closed_at ?? now()));
    }

    public function getUser(): User
    {
        return User::fromArray([
            'id' => $this->telegram_user_id,
            'is_bot' => $this->isBot,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'username' => $this->username,
            'language_code' => $this->languageCode,
            'is_premium' => $this->isPremium,
        ]);
    }

    /**
     * Создает новый экземпляр на основе массива
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: ChatStatus::from($data['status'] ?? "new"),
            telegram_user_id: $data['telegram_user_id'],
            topicId: $data['topicId'],
            firstName: $data['firstName'] ?? '',
            lastName: $data['lastName'] ?? '',
            username: $data['username'] ?? null,
            isBot: $data['isBot'] ?? false,
            languageCode: $data['languageCode'] ?? 'en',
            isPremium: $data['isPremium'] ?? false,
            is_banned: $data['is_banned'] ?? false,
            last_message_from: $data['last_message_from'] ?? 'user',
            created_at: $data['created_at'] ?? now()->toIso8601String(),
            closed_at: $data['closed_at'] ?? null,
            closed_reason: $data['closed_reason'] ?? null,
        );
    }

    /**
     * Конвертирует сессию в массив для хранения
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'telegram_user_id' => $this->telegram_user_id,
            'topicId' => $this->topicId,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'isBot' => $this->isBot,
            'languageCode' => $this->languageCode,
            'isPremium' => $this->isPremium,
            'is_banned' => $this->is_banned,
            'last_message_from' => $this->last_message_from,
            'created_at' => $this->created_at,
            'closed_at' => $this->closed_at,
            'closed_reason' => $this->closed_reason,
        ];
    }
}
