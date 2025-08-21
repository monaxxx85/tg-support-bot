<?php

namespace App\Telegram\Contracts;


interface TelegramCommandInterface
{
    public function execute(TelegramMessage $message, ?string $parameter = null): void;
    public function getName(): string;
    public function getAliases(): array;
    public function getDescription(): string;
    public function isAllow(): bool;
}
