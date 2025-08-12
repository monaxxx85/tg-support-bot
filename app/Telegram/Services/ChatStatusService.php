<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Enum\ChatStatusEmojiMapper;

class ChatStatusService
{
    public function __construct(
        protected SessionRepositoryInterface $sessionRepository,
        protected TelegramClientInterface $telegramClient,
        protected TelegramConfig $config,
    ) {
    }

    public function updateStatus(int $telegramUserId, ChatStatus $status): void
    {
        $session = $this->sessionRepository->findByUser($telegramUserId);
        if (!$session) {
            return;
        }

        if ($session->status == $status) {
            return;
        }

        $emoji = ChatStatusEmojiMapper::getEmoji($status);
        $this->telegramClient->editForumTopic(
            $this->config->supportGroupId,
            $session->topicId,
            null,
            $emoji->value
        );

        $session->status = $status;
        $this->sessionRepository->saveSession($session);
    }

    public function markAsBannedByUser(int $telegramUserId): void
    {
        $this->updateStatus($telegramUserId, ChatStatus::BANNED);
    }
}
