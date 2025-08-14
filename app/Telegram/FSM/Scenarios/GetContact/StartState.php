<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

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
        return new StateId('get_contact', 'start');
    }

    public function onEnter(Context $ctx): void
    {

        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите ваш телефон:"
        );

    }

    public function handle(Event $event, Context $ctx): ?StateId
    {
        if ($event->type === 'text') {

            $ctx->bag['phone'] = trim((string)($event->data['text'] ?? ''));
            $this->telegramClient->sendMessage(
                $ctx->userId,
                "Проверьте данные:\nТелефон: {$ctx->bag['phone']}",
                null,
                [
                    ['text' => 'Подтвердить',
                        'callback_data' => CallbackData::make('get_contact', 'finish', 'confirm')],
                    ['text' => 'Изменить телефон',
                        'callback_data' => CallbackData::make('get_contact', 'start', 'edit_phone')],
                ]
            );
        }

        if ($event->type === 'callback') {
            return match ($event->data['event'] ?? null) {
                'confirm' => new StateId('get_contact', 'finished'),
                'edit_phone' => new StateId('get_contact', 'start'),
                default => null,
            };
        }

        return null;
    }
}

