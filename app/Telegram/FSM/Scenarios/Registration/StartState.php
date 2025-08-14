<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\{CallbackData, Context, Event, StateId};


final class StartState implements StateInterface
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ){}

    public function id(): StateId
    {
        return new StateId('registration', 'start');
    }

    public function onEnter(Context $ctx): void
    {

        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Регистрация. Нажми «Начать»",
            null,
            [['text' => 'Начать', 'callback_data' => CallbackData::make($this->id()->scenario,$this->id()->state,'begin')]]

        );

    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        if ($event->type === 'callback' && ($event->data['event'] ?? null) === 'begin') {
            return new StateId('registration', 'awaiting_name');
        }
        return null;
    }
}

