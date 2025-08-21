<?php

namespace App\Telegram\Resolvers;

use App\Telegram\Contracts\TelegramChatMember;
use App\Telegram\Contracts\TelegramUpdate;

class ChatMemberUpdateResolver
{
    public function isUserBannedBot(TelegramUpdate $update): bool
    {
        return $update->new()->status() === TelegramChatMember::STATUS_KICKED
            && (int)$update->chat()->id() === (int)$update->from()->id();
    }

    public function isUserJoinedGroup(TelegramUpdate $update): bool
    {
        return $update->new()->status() === TelegramChatMember::STATUS_MEMBER
            && $update->previous()->status() !== TelegramChatMember::STATUS_MEMBER;
    }

    public function isUserLeftGroup(TelegramUpdate $update): bool
    {
        return in_array(
            $update->new()->status(), 
            [TelegramChatMember::STATUS_LEFT, TelegramChatMember::STATUS_KICKED])
            && $update->previous()->status() === TelegramChatMember::STATUS_MEMBER;
    }

}
