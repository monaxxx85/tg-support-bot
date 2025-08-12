<?php

namespace App\Telegram\Contracts;

use DefStudio\Telegraph\DTO\Message;

interface SupportChatInterface
{
    public function handleUserMessage(Message $message): void;

    public function handleSupportReply(Message $message): void;
}
