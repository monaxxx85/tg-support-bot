<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\{Context, Event, StateId};
use App\Telegram\Contracts\TelegramClientInterface;

final class FinishedState implements StateInterface
{
    public function __construct(
        protected readonly TelegramClientInterface    $telegramClient,
        protected readonly ContextRepositoryInterface $contextRepository,
        protected readonly SessionRepositoryInterface $sessionRepository,
    )
    {
    }

    public function id(): StateId
    {
        return new StateId('get_contact', 'finished');
    }

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Готово! 👌"
        );

        $session = $this->sessionRepository->findByUser($ctx->userId);
        $session->phoneNumber = $ctx->bag['phone'];
        $this->sessionRepository->saveSession($session);

        $this->contextRepository->reset($ctx->userId);
        // здесь можно сбросить контекст или оставить
    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        return null;
    }
}
