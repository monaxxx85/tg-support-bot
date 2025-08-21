<?php

namespace App\Telegram\Adapters;

use App\Telegram\Contracts\TelegramChat;
use \DefStudio\Telegraph\DTO\Chat;


class DefStudioChat implements TelegramChat
{
    protected Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function id(): string
    {
        return $this->chat->id();
    }
    public function type(): string
    {
        return $this->chat->type();
    }
    public function title(): string
    {
        return $this->chat->title();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'type' => $this->type(),
            'title' => $this->title(),
        ];
    }


}