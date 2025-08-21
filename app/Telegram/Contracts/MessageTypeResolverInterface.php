<?php

namespace App\Telegram\Contracts;


interface MessageTypeResolverInterface
{
    public function isPrivate(TelegramMessage $message): bool;
    public function isReplyInTopic(TelegramMessage $message, int $chatId = null): bool;
    public function isReplyChat(TelegramMessage $message, int $chatId = null): bool;
    public function isSystemMessageArray(array $rawMessage): bool;
}
