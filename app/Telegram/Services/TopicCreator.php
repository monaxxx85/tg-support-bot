<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Enum\TelegramEmojiEnum;
use App\Telegram\Presenters\UserPresenter;
use DefStudio\Telegraph\DTO\Message;

class TopicCreator
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
        protected readonly UserPresenter           $userPresenter
    )
    {
    }

    public function createForMessage(Message $message, int $groupId): int
    {
        $name = $this->userPresenter->formatTopicName($message->from());
        $topicId = $this->telegramClient->createForumTopic($groupId, $name, TelegramEmojiEnum::QuestionIcon->value);


        $this->telegramClient->sendMessage(
            $groupId,
            $this->userPresenter->contact($message->from()),
            $topicId
        );

        return $topicId;
    }

}
