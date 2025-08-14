<?php

// app/Telegram/FSM/Scenarios/Registration/AwaitingEmailState.php
namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Contracts\StateInterface;
use App\Telegram\FSM\Core\{Context, Event, StateId};
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Core\CallbackData;

final class AwaitingEmailState implements StateInterface
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ){}

    public function id(): StateId
    {
        return new StateId('registration', 'awaiting_email');
    }

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите email:"
        );
    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        if ($event->type === 'text') {
            $ctx->bag['email'] = trim((string)$event->data['text']);

            $this->telegramClient->sendMessage(
                $ctx->userId,
                "Проверьте данные:\nИмя: {$ctx->bag['name']}\nEmail: {$ctx->bag['email']}",
                null,
                [
                    ['text' => 'Подтвердить',
                        'callback_data' => CallbackData::make('registration', 'awaiting_email', 'confirm')],
                    ['text' => 'Изменить имя',
                        'callback_data' => CallbackData::make('registration', 'awaiting_email', 'edit_name')],
                ]
            );

        }

        if ($event->type === 'callback') {
            return match ($event->data['event'] ?? null) {
                'confirm' => new StateId('registration', 'finished'),
                'edit_name' => new StateId('registration', 'awaiting_name'),
                default => null,
            };
        }

        return null;
    }
}

