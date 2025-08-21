<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\FSM\Abstract\AbstractState;
use App\Telegram\FSM\Core\{Context, Event, StateId};
use App\Telegram\Contracts\TelegramClientInterface;

final class FinishedState extends AbstractState
{
    public function __construct(
        protected readonly TelegramClientInterface    $telegramClient,
        protected readonly SessionRepositoryInterface $sessionRepository,
    ){}


    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Готово! 👌"
        );

        $session = $this->sessionRepository->findByUser($ctx->userId);
        $session->phoneNumber = $ctx->bag['phone'];
        $this->sessionRepository->saveSession($session);

    }

}
