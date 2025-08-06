<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Presenters\UserPresenter;
use DefStudio\Telegraph\DTO\Message;


class SupportChatService
{
    protected int $supportGroupId;

    public function __construct(
        protected SessionRepositoryInterface $sessionRepository,
        protected TelegramClientInterface    $telegramClient,
        protected UserPresenter              $userPresenter,
        protected TopicCreator               $topicCreator,

    )
    {
        $this->supportGroupId = config('telegraph.support_group_id');
    }

    public function handleUserMessage(Message $message): void
    {
        $session = $this->sessionRepository->findByUser($message->from()->id());

        if (!$session) {
            $session = $this->sessionRepository->createSession($message);
            $topicId = $this->topicCreator->createForMessage($message, $this->supportGroupId);

            $session->topicId = $topicId;
            $this->sessionRepository->saveSession($session);
        }

        $this->telegramClient->forwardMessage(
            $this->supportGroupId,
            $message->from()->id(),
            $message->id(),
            $session->topicId
        );

    }

    public function handleSupportReply(Message $message): void
    {
        $topicId = $message->replyToMessage()?->messageThreadId();
        $session = $this->sessionRepository->findByTopic($topicId);

        $this->telegramClient->forwardMessage(
            $session->telegram_user_id,
            $this->supportGroupId,
            $message->id()
        );

    }

}
