<?php

namespace App\Telegram\Commands\SupportChat;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Formatters\HtmlFormatter;
use App\Telegram\Presenters\SessionPresenter;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\DTO\TelegramConfig;


class InfoSessionCommand extends BaseCommand
{
    public function __construct(
        protected TelegramClientInterface $telegramClient,
        protected SessionRepositoryInterface $sessionRepository,
        protected TelegramConfig $config,
    ) {}

    public function getName(): string
    {
        return 'info_session';
    }

    public function getAliases(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return "Получить все данные из сессии";
    }

    public function execute(Message $message, ?string $parameter = null): void
    {
        $topicId = $message->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        if (!$session)
            return;

        $this->telegramClient
            ->sendMessage(
                $this->config->supportGroupId,
                (new HtmlFormatter())
                    ->render(new SessionPresenter($session)),
                $topicId
            );

    }
}
