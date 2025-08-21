<?php

namespace App\Telegram\Adapters\Arrays;

use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUser;
use App\Telegram\Contracts\TelegramChat;

class ArrayTelegramMessage implements TelegramMessage
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function text(): string
    {
        return $this->data['text'] ?? '';
    }

    public function from(): TelegramUser
    {
        return new ArrayTelegramUser($this->data['from'] ?? []);
    }

    public function chat(): TelegramChat
    {
        return new ArrayTelegramChat($this->data['chat'] ?? []);
    }

    public function id(): int
    {
        return $this->data['message_id'] ?? 0;
    }

    public function date(): int
    {
        return $this->data['date'] ?? time();
    }

    public function replyToMessage(): ?TelegramMessage
    {
        if (isset($this->data['reply_to_message']) && is_array($this->data['reply_to_message'])) {
            return new self($this->data['reply_to_message']);
        }
        return null;
    }

    public function messageThreadId(): ?int 
    {
        return $this->data['message_thread_id'] ?? null;
    }   

    public function toArray(): array
    {
        return $this->data;
    }


}