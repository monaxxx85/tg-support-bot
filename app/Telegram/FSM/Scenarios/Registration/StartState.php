<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Core\{CallbackData, Context, Event, EventBus, StateId};
use App\Telegram\FSM\Abstract\AbstractState;

final class StartState extends AbstractState
{
    public function __construct(
        protected TelegramClientInterface $telegramClient,
    ) {}

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Регистрация. Нажми «Начать»",
            null,
            [[
                    'text' => 'Начать',
                    'callback_data' => CallbackData::make($this->id()->scenario, $this->id()->state, 'begin')
            ]]);
    }

    public function handleCallbackQuery(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return  $this->nextInOrder();
    }


}

