<?php

namespace App\Telegram\Commands;

use App\Telegram\Contracts\TelegramCommandInterface;
use DefStudio\Telegraph\DTO\Message;

abstract class BaseCommand implements TelegramCommandInterface
{
    /**
     * Выполнение команды
     * @param \DefStudio\Telegraph\DTO\Message $message
     * @param mixed $parameter
     * @return void
     */
    abstract public function execute(Message $message, ?string $parameter = null): void;

    /**
     * Основное имя команды (например, "start" )
     * @return void
     */
    abstract public function getName(): string;

    /**
     * Описание команды для справки
     * @return void
     */
    abstract public function getDescription(): string;

    /**
     * Псевдонимы команды (другие варианты вызова)
     * @return array<string>
     */
    public function getAliases(): array
    {
        return [];
    }

    public function isAllow(): bool
    {
        return true;
    }

}
