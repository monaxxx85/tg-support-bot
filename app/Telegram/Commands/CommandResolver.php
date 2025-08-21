<?php

namespace App\Telegram\Commands;

use App\Telegram\Contracts\TelegramCommandInterface;

class CommandResolver
{
    /**
     * @var TelegramCommandInterface[]
     */
    protected array $commands = [];

    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    public function resolve(string $name): ?TelegramCommandInterface
    {
        foreach ($this->commands as $command) {

            if ($command->getName() === $name
                || in_array($name, $command->getAliases(), true)) {

                if ($command->isAllow())
                    return $command;

            }
        }
        return null;
    }
}
