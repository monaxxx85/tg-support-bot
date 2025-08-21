<?php

namespace App\Telegram\Contracts;

interface SupportChatInterface
{
    public function handleUserMessage(TelegramMessage $message): void;

    public function handleSupportReply(TelegramMessage $message): void;
}
