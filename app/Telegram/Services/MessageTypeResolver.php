<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use DefStudio\Telegraph\DTO\Chat;
use DefStudio\Telegraph\DTO\Message;

class MessageTypeResolver implements MessageTypeResolverInterface
{
    public function __construct(
        private readonly int $supportGroupId
    ){}

    public function isPrivate(Message $message): bool
    {
        return $message->chat()?->type() === Chat::TYPE_PRIVATE;
    }

    public function isReplyInTopic(Message $message): bool
    {
        return $message->replyToMessage()
            && $message->chat()?->id() == $this->supportGroupId;
    }

}
