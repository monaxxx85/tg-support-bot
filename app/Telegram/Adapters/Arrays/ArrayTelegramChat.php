<?php

namespace App\Telegram\Adapters\Arrays;

use App\Telegram\Contracts\TelegramChat;

class ArrayTelegramChat implements TelegramChat
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function id(): string
    {
        return $this->data['id'] ?? 0;
    }

    public function type(): string
    {
        return $this->data['type'] ?? 'private';
    }

    public function title(): string
    {
        return $this->data['title'] ?? '';
    }

    public function toArray(): array
    {
        return $this->data;
    }
}