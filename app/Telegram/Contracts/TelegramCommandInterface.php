<?php

namespace App\Telegram\Contracts;

use DefStudio\Telegraph\DTO\Message;

interface TelegramCommandInterface
{
    public function execute(Message $message, ?string $parameter = null): void;
    public function getName(): string;
    public function getAliases(): array;
    public function getDescription(): string;
    public function isAllow(): bool;
}
