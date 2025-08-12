<?php

namespace App\Telegram\Contracts;

use DefStudio\Telegraph\DTO\Message;

interface MessageTypeResolverInterface
{
    public function isPrivate(Message $message): bool;
    public function isReplyInTopic(Message $message, int $chatId = null): bool;
    public function isReplyChat(Message $message, int $chatId = null): bool;
    public function isSystemMessageArray(array $rawMessage): bool;
}
