<?php

namespace App\Telegram\Commands\SupportChat;


use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Core\FSM;
use DefStudio\Telegraph\DTO\Message;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Presenters\UserPresenter;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Formatters\ContactUserFormatter;

class GetContactCommand extends BaseCommand
{
    protected int $supportChatId;

    public function __construct(
        protected FSM                        $fsm,
        protected SessionRepositoryInterface $sessionRepository,
    )
    {
    }

    public function getName(): string
    {
        return 'get_contact';
    }

    public function getAliases(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return "Получить телефон от пользователя";
    }

    public function execute(Message $message, ?string $parameter = null): void
    {
        $topicId = $message->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        if (!$session)
            return;

        $this->fsm->start('get_contact', $session->telegram_user_id, $session->telegram_user_id, [], 1);

    }
}
