<?php

namespace App\Telegram\Contracts;

use App\Telegram\DTO\FrontResponse;


interface TelegramWebhookGatewayInterface
{
    public function handleChatMessage(TelegramMessage $message): FrontResponse;

    public function handleCommand(TelegramMessage $message, string $command, string $parameter): FrontResponse;

    public function handleCallbackQuery(TelegramCallbackQuery $callbackQuery): FrontResponse;

    public function handleBotChatStatusUpdate(TelegramUpdate $update): FrontResponse;

    public function getUserId(): int;

    public function getChatId(): int;

    public function getMessageId(): int;

    public function isAdmin(): bool;
}
