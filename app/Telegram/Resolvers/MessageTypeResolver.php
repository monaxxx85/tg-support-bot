<?php

namespace App\Telegram\Resolvers;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\TelegramChat;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\DTO\TelegramConfig;

class MessageTypeResolver implements MessageTypeResolverInterface
{
    public function __construct(
        protected readonly TelegramConfig $config,
    ) {
    }

    public function isPrivate(TelegramMessage $message): bool
    {
        return $message->chat()?->type() === TelegramChat::TYPE_PRIVATE;
    }

    public function isReplyInTopic(TelegramMessage $message, ?int $chatId = null): bool
    {
        return $message->replyToMessage()
            && $message->chat()?->id() == ($chatId ?? $this->config->supportGroupId)
            && $message->replyToMessage()?->messageThreadId();
    }

    public function isReplyChat(TelegramMessage $message, ?int $chatId = null): bool
    {
        return $message->replyToMessage() === null
            && $message->chat()?->id() == ($chatId ?? $this->config->supportGroupId);
    }


    public function isSystemMessageArray(array $rawMessage): bool
    {
        $systemKeys = [
            'forum_topic_created',
            'forum_topic_edited',
            'forum_topic_closed',
            'forum_topic_reopened',
            'pinned_message',
            'new_chat_members',
            'left_chat_member',
        ];

        foreach ($systemKeys as $k) {
            if (!empty($rawMessage[$k])) {
                return true;
            }
        }
        return false;
    }

}
