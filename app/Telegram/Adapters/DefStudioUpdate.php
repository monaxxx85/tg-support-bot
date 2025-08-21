<?php

namespace App\Telegram\Adapters;


use App\Telegram\Contracts\TelegramChat;
use App\Telegram\Contracts\TelegramChatMember;
use App\Telegram\Contracts\TelegramUpdate;
use App\Telegram\Contracts\TelegramUser;
use App\Telegram\DTO\User;
use \DefStudio\Telegraph\DTO\ChatMemberUpdate;
use Carbon\CarbonInterface;


class DefStudioUpdate implements TelegramUpdate
{
    protected ChatMemberUpdate $update;

    public function __construct(ChatMemberUpdate $update)
    {
        $this->update = $update;
    }

    public function date(): ?CarbonInterface
    {
        return $this->update->date();
    }

    public function from(): ?TelegramUser
    {
        $from = $this->update->from();
        return $from ? User::fromArray($from->toArray()) : null;
    }

    public function chat(): ?TelegramChat
    {
        $chat = $this->update->chat();
        return $chat ? new DefStudioChat($chat) : null;
    }

    public function previous(): ?TelegramChatMember
    {
        $previous = $this->update->previous();
        return $previous ? new DefStudioChatMember($previous) : null;
    }

    public function new(): ?TelegramChatMember
    {
        $new = $this->update->new();
        return $new ? new DefStudioChatMember($new) : null;
    }

    public function toArray(): array
    {
        return $this->update->toArray();
    }


}
