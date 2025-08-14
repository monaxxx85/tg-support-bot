<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\{Context, Event, StateId};
use App\Telegram\Contracts\TelegramClientInterface;

final class AwaitingNameState implements StateInterface
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ){}

    public function id(): StateId
    { return new StateId('registration', 'awaiting_name'); }

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите имя:"
        );
    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        if ($event->type === 'text') {
            $ctx->bag['name'] = trim((string)($event->data['text'] ?? ''));
            return new StateId('registration', 'awaiting_email');
        }
        return null;
    }
}
