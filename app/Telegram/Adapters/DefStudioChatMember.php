<?php

namespace App\Telegram\Adapters;


use App\Telegram\Contracts\TelegramChatMember;
use App\Telegram\Contracts\TelegramUser;
use App\Telegram\DTO\User;
use \DefStudio\Telegraph\DTO\ChatMember;


class DefStudioChatMember implements TelegramChatMember
{
    protected ChatMember $chatMember;

    public function __construct(ChatMember $chatMember)
    {
        $this->chatMember = $chatMember;
    }
    public function status(): string
    {
        return $this->chatMember->isAnonymous();
    }
    public function user(): ?TelegramUser
    {
        $user = $this->chatMember->user();
        return $user ? User::fromArray($user->toArray()) : null;
    }

    public function isAnonymous(): bool
    {
        return $this->chatMember->isAnonymous();
    }

    public function custom_title(): string
    {
        return $this->chatMember->custom_title();
    }

    public function is_member(): bool
    {
        return $this->chatMember->is_member();
    }
    public function until_date(): ?int
    {
        return $this->chatMember->until_date();
    }

    public function toArray(): array
    {
        return $this->chatMember->toArray();
    }


}
