<?php

namespace App\Telegram\Commands;

use App\Telegram\Contracts\TelegramCommandInterface;
use App\Telegram\Contracts\TelegramMessage;


abstract class BaseCommand implements TelegramCommandInterface
{

    abstract public function execute(TelegramMessage $message, ?string $parameter = null): void;

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
