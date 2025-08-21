<?php

// app/Telegram/FSM/Scenarios/Registration/AwaitingEmailState.php
namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\FSM\Abstract\AbstractState;
use App\Telegram\FSM\Core\{Context, Event, EventBus, StateId};
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\FSM\Core\CallbackData;

final class AwaitingEmailState extends AbstractState
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
    ) {
    }

    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Введите email:"
        );
    }

    public function handleChatMessage(Event $event, Context $context, ?EventBus $eventBus = null): ?StateId
    {
        $email = trim((string) ($event->data['text'] ?? ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->telegramClient->sendMessage(
                $context->userId,
                "Пожалуйста, введите корректный email адрес."
            );
            // Остаемся в текущем состоянии
            return null;
        }

        $context->bag['email'] = $email;

            $this->telegramClient->sendMessage(
                $context->userId,
                "Проверьте данные:\nИмя: {$context->bag['name']}\nEmail: {$context->bag['email']}",
                null,
                [
                    ['text' => 'Подтвердить',
                        'callback_data' => CallbackData::make(
                            $this->id()->scenario,
                            $this->id()->state,
                            'confirm'
                            )],
                    ['text' => 'Изменить имя',
                        'callback_data' => CallbackData::make(
                            $this->id()->scenario,
                            $this->id()->state,
                            'edit_name'
                            )],
                ]
            );

            return null;
    }

    public function handleCallbackQuery(Event $event, Context $context, ? EventBus $eventBus = null): ?StateId
    {
        return match ($event->name()) {
                'confirm' => $this->finish(),
                'edit_name' => $this->next('awaiting_name'),
                default => null,
            };
    }
}

