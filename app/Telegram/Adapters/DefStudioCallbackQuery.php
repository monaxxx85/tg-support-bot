<?php

namespace App\Telegram\Adapters;

use App\Telegram\Contracts\TelegramCallbackQuery;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUser;
use App\Telegram\DTO\User;
use DefStudio\Telegraph\DTO\CallbackQuery;
use Illuminate\Support\Collection;


class DefStudioCallbackQuery implements TelegramCallbackQuery
{
    protected CallbackQuery $callbackQuery;

    public function __construct(CallbackQuery $callbackQuery)
    {
        $this->callbackQuery = $callbackQuery;
    }

    public function id(): int
    {
        return $this->callbackQuery->id();
    }

    public function from(): ?TelegramUser
    {
        $from = $this->callbackQuery->from();
        return $from ? User::fromArray($from->toArray()) : null;
    }

    public function message(): ?TelegramMessage
    {
        $message = $this->callbackQuery->message();
        return $message ? new DefStudioMessage($message) : null;
    }

    public function data(): ?Collection
    {
        return $this->callbackQuery->data();
    }

    public function toArray(): array
    {
        return $this->callbackQuery->toArray();
    }



}