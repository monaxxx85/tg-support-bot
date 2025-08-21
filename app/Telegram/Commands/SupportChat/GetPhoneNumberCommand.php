<?php

namespace App\Telegram\Commands\SupportChat;


use App\Telegram\FSM\Core\FSM;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;


class GetPhoneNumberCommand extends BaseCommand
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
        return 'get_phone';
    }

    public function getAliases(): array
    {
        return ['get-phone'];
    }

    public function getDescription(): string
    {
        return "Получить телефон от пользователя";
    }

    public function execute(TelegramMessage $message, ?string $parameter = null): void
    {
        $topicId = $message->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        if (!$session)
            return;

        $this->fsm->start(
            'get_contact',
            $session->telegram_user_id,
            $session->telegram_user_id,
            [],
            1
        );

    }
}
