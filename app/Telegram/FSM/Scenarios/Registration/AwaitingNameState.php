<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Abstract\AbstractState;

use App\Telegram\FSM\Core\{Context, Event, EventBus, StateId};
use App\Telegram\Contracts\TelegramClientInterface;

final class AwaitingNameState extends AbstractState
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ){}


    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите имя:"
        );
    }

    public function handleChatMessage(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        $context->bag['name'] = trim((string)($event->data['text'] ?? ''));

        return $this->nextInOrder();
    }
}
