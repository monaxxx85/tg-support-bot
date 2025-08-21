<?php

namespace App\Telegram\DTO;

use Illuminate\Support\Carbon;
use App\Telegram\Enum\ChatStatus;
use Illuminate\Contracts\Support\Arrayable;

class ChatSession implements Arrayable
{
    public function __construct(
        public ChatStatus $status,
        public readonly int $telegram_user_id,
        public int $topicId,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $username,
        public ?string $phoneNumber,
        public bool $isBot,
        public string $languageCode,
        public bool $isPremium,
        public string $created_at,
        public ?string $closed_at = null,
        public ?string $closed_reason = null,
        public ?string $state_fsm = null, // registration:awaiting_name
        public ?int $chat_id_fsm = null,
        public ?array $bag_fsm = [],
        public ?string $ttl_at_fsm = null,
        public bool $is_async_pending_fsm = false,
        public ?string $async_job_id_fsm = null,
        public bool $is_banned = false,
        public string $last_message_from = 'user', // 'user' | 'support'
    ) {}

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
        return new User(
            $this->telegram_user_id,
            $this->username,
            $this->isBot,
            $this->firstName,
            $this->lastName,
            $this->languageCode,
            $this->isPremium,
        );
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
            phoneNumber: $data['phone_number'] ?? null,
            isBot: $data['isBot'] ?? false,
            languageCode: $data['languageCode'] ?? 'en',
            isPremium: $data['isPremium'] ?? false,
            is_banned: $data['is_banned'] ?? false,
            last_message_from: $data['last_message_from'] ?? 'user',
            created_at: $data['created_at'] ?? now()->toIso8601String(),
            closed_at: $data['closed_at'] ?? null,
            closed_reason: $data['closed_reason'] ?? null,
            state_fsm: $data['state_fsm'] ?? null,
            chat_id_fsm: $data['chat_id_fsm'] ?? null,
            bag_fsm: $data['bag_fsm'] ?? [],
            ttl_at_fsm: $data['ttl_at_fsm'] ?? null,
            is_async_pending_fsm: $data['is_async_pending_fsm'] ?? false,
            async_job_id_fsm: $data['async_job_id_fsm'] ?? '',
        );
    }


    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'telegram_user_id' => $this->telegram_user_id,
            'topicId' => $this->topicId,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'phone_number' => $this->phoneNumber,
            'isBot' => $this->isBot,
            'languageCode' => $this->languageCode,
            'isPremium' => $this->isPremium,
            'is_banned' => $this->is_banned,
            'last_message_from' => $this->last_message_from,
            'created_at' => $this->created_at,
            'closed_at' => $this->closed_at,
            'closed_reason' => $this->closed_reason,

            //Context
            'state_fsm' => $this->state_fsm,
            'chat_id_fsm' => $this->chat_id_fsm,
            'bag_fsm' => $this->bag_fsm,
            'ttl_at_fsm' => $this->ttl_at_fsm,
            'is_async_pending_fsm' => $this->is_async_pending_fsm,
            'async_job_id_fsm' => $this->async_job_id_fsm,

        ];
    }
}
