<?php

namespace App\Telegram\Adapters;

use App\Telegram\Contracts\TelegramChat;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUser;
use App\Telegram\DTO\User;
use \DefStudio\Telegraph\DTO\Message;


class DefStudioMessage implements TelegramMessage
{
    protected Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function from(): ?TelegramUser
    {
        $from = $this->message->from();
        return $from ? User::fromArray($from->toArray()) : null;
    }
    public function chat(): ?TelegramChat
    {
        $chat = $this->message->chat();
        return $chat ? new DefStudioChat($chat) : null;
    }
    public function replyToMessage(): ?TelegramMessage
    {
        $reply = $this->message->replyToMessage();
        return $reply ? new self($reply) : null;
    }
    public function id(): ?int
    {
        return $this->message->id();
    }
    public function text(): ?string
    {
        return $this->message->text();
    }
    public function messageThreadId(): ?int
    {
        return $this->message->messageThreadId();
    }

    public function toArray(): array
    {
        return $this->message->toArray();
    }

}