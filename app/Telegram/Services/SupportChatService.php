<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Enum\ChatStatus;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Contracts\SupportChatInterface;

class SupportChatService implements SupportChatInterface
{
    protected int $supportGroupId;

    public function __construct(
        protected readonly SessionRepositoryInterface $sessionRepository,
        protected readonly TelegramClientInterface $telegramClient,
        protected readonly TopicCreator $topicCreator,
        protected readonly TelegramConfig $config,
        protected readonly ChatStatusService $chatStatusService

    ) {
        $this->supportGroupId = $this->config->supportGroupId;
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

        $this->telegramClient->copyMessage(
            $this->supportGroupId,
            $message->chat()->id(),
            $message->id(),
            $session->topicId
        );


        $session->last_message_from = 'user';
        $this->sessionRepository->saveSession($session);

    }

    public function handleSupportReply(Message $message): void
    {
        $topicId = $message->replyToMessage()?->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        $this->telegramClient->copyMessage(
            $session->telegram_user_id,
            $message->chat()->id(),
            $message->id()
        );

        $session->last_message_from = 'support';
        $this->sessionRepository->saveSession($session);


        if ($session->status !== ChatStatus::OPEN) {
            $this->chatStatusService->updateStatus($session->telegram_user_id, ChatStatus::OPEN);
        }
    }

}
