<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\TelegramUser;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class TelegramAuth implements Arrayable
{
    private ?int $userId = null;
    private ?int $chatId = null;
    private ?int $messageId = null;
    private bool $isAdmin = false;
    private ?string $sessionStartAt = null;

    public function init(int $userId, int $chatId, bool $isAdmin = false, ?int $messageId = null): void
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->isAdmin = $isAdmin;
        $this->sessionStartAt = now()->toIso8601String();
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    public function chatId(): ?int
    {
        return $this->chatId;
    }

    public function messageId(): ?int
    {
        return $this->messageId;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function sessionDuration(): int
    {
        if (!$this->sessionStartAt) {
            return 0;
        }

        return (int) Carbon::parse($this->sessionStartAt)->diffInMinutes(now());
    }

    public function isAuthenticated(): bool
    {
        return $this->userId !== null && $this->chatId !== null;
    }

    public function user(): ?TelegramUser
    {
        if($this->userId === null) {
            return null;
        }

        return app(App\Telegram\Contracts\SessionRepositoryInterface::class)->findByUser($this->userId);
    }

    public function reset(): void
    {
        $this->userId = null;
        $this->chatId = null;
        $this->messageId = null;
        $this->isAdmin = false;
        $this->sessionStartAt = null;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'is_admin' => $this->isAdmin,
            'session_start_at' => $this->sessionStartAt,
            'is_authenticated' => $this->isAuthenticated()
        ];
    }
}
