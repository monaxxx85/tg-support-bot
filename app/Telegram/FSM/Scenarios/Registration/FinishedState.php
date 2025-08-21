<?php

namespace App\Telegram\FSM\Scenarios\Registration;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\FSM\Abstract\AbstractState;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\FSM\Core\Context;
use App\Telegram\Contracts\TelegramClientInterface;

final class FinishedState extends AbstractState
{
    public function __construct(
        protected readonly TelegramClientInterface $telegramClient,
        protected readonly ContextRepositoryInterface $contextRepository,
        protected readonly SessionRepositoryInterface $sessionRepository,
        protected readonly TelegramConfig $config,
    ) {
    }


    public function onEnter(Context $ctx): void
    {
        $this->telegramClient->sendMessage(
            $ctx->userId,
            "Готово! Регистрация завершена 👌"
        );

        $session = $this->sessionRepository->findByUser($ctx->userId);
        if ($session !== null && $session->topicId > 0) {
            $this->telegramClient->sendMessage(
                $this->config->supportGroupId,
                "Пользователь завершил регистрацию! \nИмя: {$ctx->bag['name']}\nEmail: {$ctx->bag['email']}",
                $session->topicId
            );
        }
    }

  
}
