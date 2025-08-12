<?php

namespace App\Telegram\Commands\SupportChat;


use App\Telegram\Contracts\TelegramClientInterface;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Presenters\UserPresenter;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Formatters\ContactUserFormatter;

class ContactCommand extends BaseCommand
{
    protected int $supportChatId;

    public function __construct(
        protected TelegramClientInterface $telegramClient,
        protected TelegramConfig $config,
        protected SessionRepositoryInterface $sessionRepository,
    ) {
        $this->supportChatId = $config->supportGroupId;
    }

    public function getName(): string
    {
        return 'contact';
    }

    public function getAliases(): array
    {
        return ['контакт'];
    }

    public function getDescription(): string
    {
        return "Получить данные пользователя";
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
                $this->supportChatId,
                (new ContactUserFormatter())
                    ->render(new UserPresenter($session->getUser())),
                $topicId
            );
    }
}
