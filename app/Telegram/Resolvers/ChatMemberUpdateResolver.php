<?php

namespace App\Telegram\Resolvers;

use DefStudio\Telegraph\DTO\ChatMember;
use DefStudio\Telegraph\DTO\ChatMemberUpdate;

class ChatMemberUpdateResolver
{
    public function isUserBannedBot(ChatMemberUpdate $update): bool
    {
        return $update->new()->status() === ChatMember::STATUS_KICKED
            && (int)$update->chat()->id() === (int)$update->from()->id();
    }

    public function isUserJoinedGroup(ChatMemberUpdate $update): bool
    {
        return $update->new()->status() === ChatMember::STATUS_MEMBER
            && $update->previous()->status() !== ChatMember::STATUS_MEMBER;
    }

    public function isUserLeftGroup(ChatMemberUpdate $update): bool
    {
        return in_array($update->new()->status(), [ChatMember::STATUS_LEFT, ChatMember::STATUS_KICKED])
            && $update->previous()->status() === ChatMember::STATUS_MEMBER;
    }

}
