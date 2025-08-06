<?php

namespace App\Telegram\Services;

use DefStudio\Telegraph\DTO\Chat;
use DefStudio\Telegraph\DTO\Message;

class MessageService
{
    public function __construct(
        private int $supportGroupId
    )
    {
    }

    public function isPrivate($message): bool
    {
        return $message?->chat()?->type() === Chat::TYPE_PRIVATE;
    }

    public function isReplyTopicChat($message): bool
    {
        return
            $message?->replyToMessage() &&
            $message->chat()->id() == $this->supportGroupId;

            //env('TELEGRAM_SUPPORT_GROUP_ID');
    }
}
