<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\{Context, Event, StateId};
use App\Telegram\Contracts\TelegramClientInterface;

final class FinishedState implements StateInterface
{
    public function __construct(
        protected readonly TelegramClientInterface    $telegramClient,
        protected readonly ContextRepositoryInterface $contextRepository,
    )
    {
    }

    public function id(): StateId
    {
        return new StateId('registration', 'finished');
    }

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Готово! Регистрация завершена 👌"
        );

        $this->contextRepository->reset($ctx->userId);
        // здесь можно сбросить контекст или оставить
    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        return null;
    }
}
