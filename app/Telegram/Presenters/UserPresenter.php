<?php

namespace App\Telegram\Presenters;

use App\Telegram\Contracts\TelegramUser;
use App\Telegram\Presenters\BasePresenter;

class UserPresenter extends BasePresenter
{
    public function __construct(private readonly TelegramUser $user)
    {
    }

    public function topicName(): string
    {
        if ($this->user->firstName() && $this->user->lastName()) {
            return trim($this->user->firstName() . ' ' . $this->user->lastName());
        }

        if ($this->user->username()) {
            return '@' . $this->user->username();
        }

        return "ID: {$this->user->id()}";
    }

    public function toArray(): array
    {
        return [
            'full_name' => trim($this->user->firstName() . ' ' . $this->user->lastName()),
            'name' => trim($this->user->firstName()),
            'username' => $this->user->username(),
            'id' => (string) $this->user->id(),
            'is_bot' => $this->user->isBot(),
        ];
    }

}
