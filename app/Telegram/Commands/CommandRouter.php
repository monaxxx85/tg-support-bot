<?php

namespace App\Telegram\Commands;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Contracts\TelegramCommandInterface;

class CommandRouter
{
    public function __construct(
        private readonly MessageTypeResolverInterface $messageTypeResolver,
        private readonly CommandResolver $privateCommands,
        private readonly CommandResolver $supportCommands,
        private readonly CommandResolver $generalCommands,
    ) {
    }

    public function resolve(Message $message, string $name): ?TelegramCommandInterface
    {
        if ($this->messageTypeResolver->isPrivate($message)) {
            return $this->privateCommands->resolve($name);
        }

        if ($this->messageTypeResolver->isReplyInTopic($message)) {
            return $this->supportCommands->resolve($name);
        }

        if ($this->messageTypeResolver->isReplyChat($message)) {
            return $this->generalCommands->resolve($name);
        }

        return null;
    }
}
