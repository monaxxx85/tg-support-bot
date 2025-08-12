<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Presenters\UserPresenter;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Enum\ChatStatusEmojiMapper;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Formatters\ContactUserFormatter;
use App\Telegram\Enum\TelegramEmoji;

class TopicCreator
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    )
    {}

    public function createForMessage(Message $message, int $groupId): int
    {

        $userPresenter = new UserPresenter($message->from());
        $nameTopic = $userPresenter->topicName();

        /**
         * @var TelegramEmoji
         */
        $emoji = ChatStatusEmojiMapper::getEmoji(ChatStatus::NEW);

        $topicId = $this->telegramClient->createForumTopic($groupId, $nameTopic, $emoji->value);


        $this->telegramClient->sendMessage(
            $groupId,
            (new ContactUserFormatter)->render($userPresenter),
            $topicId
        );

        return $topicId;
    }

}
