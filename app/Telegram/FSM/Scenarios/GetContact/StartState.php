<?php

namespace App\Telegram\FSM\Scenarios\GetContact;

use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Core\{CallbackData, Context, Event, EventBus, StateId};
use App\Telegram\FSM\Abstract\AbstractState;

final class StartState extends AbstractState
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ) {}


    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите ваш телефон:"
        );

    }


    public function handleChatMessage(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        $context->bag['phone'] = trim((string) ($event->data['text'] ?? ''));
        $this->telegramClient->sendMessage(
            $context->userId,
            "Проверьте данные:\nТелефон: {$context->bag['phone']}",
            null,
            [
                ['text' => 'Подтвердить',
                    'callback_data' => CallbackData::make(
                        $this->id()->scenario,
                        $this->id()->state,
                        'confirm')
                    ],
                ['text' => 'Изменить телефон',
                    'callback_data' => CallbackData::make(
                        $this->id()->scenario,
                        $this->id()->state,
                        'edit_phone')
                    ],
            ]
        );

        return null;
    }

    public function handleCallbackQuery(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        return match ($event->name()) {
            'confirm' => $this->finish(),
            'edit_phone' => $this->id(),
            default => null,
        };
    }

}

