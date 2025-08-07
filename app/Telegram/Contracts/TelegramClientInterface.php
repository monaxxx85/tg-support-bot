<?php

namespace App\Telegram\Contracts;


interface TelegramClientInterface
{

    public function sendMessage(int $chatId, string $text, ?int $threadId = null): void;
    public function copyMessage(int $toChatId, int $fromChatId, int $messageId, ?int $threadId = null): void;
    public function createForumTopic(int $chatId, string $name, ?string $emoji = null): int;
    public function editForumTopic(int $chatId, int $threadId, ?string $name, ?string $emoji = null): void;
    public function deleteForumTopic(int $chatId, int $threadId): void;


}
