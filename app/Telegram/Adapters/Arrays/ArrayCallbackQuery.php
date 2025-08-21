<?php

namespace App\Telegram\Adapters;

use App\Telegram\Contracts\TelegramCallbackQuery;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUser;
use Illuminate\Support\Collection;


class ArrayCallbackQuery implements TelegramCallbackQuery
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function id(): ?int
    {
       return $this->data['id'] ?? null;
    }

    public function from(): ?TelegramUser
    {
        return new ArrayTelegramUser($this->data['from'] ?? []);
    }

    public function message(): ?TelegramMessage
    {
       if (isset($this->data['message']) && is_array($this->data['message'])) {
            return new ArrayTelegramMessage($this->data['message']);
        }
        return null;
    }

    public function data(): ?Collection
    {
        if (!isset($this->data['data'])) {
            return null;
        }
        return new Collection($this->data['data'] ?? []);
    }

    public function toArray(): array
    {
        return $this->data;
    }



}