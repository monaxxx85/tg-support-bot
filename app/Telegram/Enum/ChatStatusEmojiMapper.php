<?php

namespace App\Telegram\Enum;

final class ChatStatusEmojiMapper
{
    private const MAP = [
        ChatStatus::NEW->value => TelegramEmoji::QUESTION_ICON,
        ChatStatus::OPEN->value => TelegramEmoji::DEFAULT_ICON,
        ChatStatus::CLOSED->value => TelegramEmoji::ANSWERED_ICON,
        ChatStatus::PENDING->value => TelegramEmoji::CLOCK_ICON,
        ChatStatus::BANNED->value => TelegramEmoji::BANNED_ICON,
    ];

    public static function getEmoji(ChatStatus $status): TelegramEmoji
    {
        return self::MAP[$status->value] ?? self::MAP[ChatStatus::NEW->value] ;
    }


}
