<?php

namespace App\Telegram;

use App\Telegram\Contracts\TelegramCallbackQuery;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUpdate;
use App\Telegram\Contracts\TelegramWebhookGatewayInterface;
use App\Telegram\DTO\FrontResponse;
use App\Telegram\FSM\Core\FSMManager;
use App\Telegram\Facades\TelegramAuth;


final class TelegramFrontController
{
    private TelegramWebhookGatewayInterface $gateway;

    public function __construct(
        private FSMManager $fsmManager
    ) {
    }

    public function withGateway(TelegramWebhookGatewayInterface $gateway): self
    {
        $clone = clone $this;
        $clone->gateway = $gateway;

        TelegramAuth::init(
            $gateway->getUserId(),
            $gateway->getChatId(),
            $gateway->isAdmin(),
            $gateway->getMessageId()
        );

        return $clone;
    }

    public function handleChatMessage(TelegramMessage $message): FrontResponse
    {
        if ($this->fsmManager->processMessage($message)) {
            return FrontResponse::stop();
        }

        return $this->gateway->handleChatMessage($message);
    }

    public function handleCommand(TelegramMessage $message, string $command, string $parameter): FrontResponse
    {
        if ($this->fsmManager->processCommand($message, $command, $parameter)) {
            return FrontResponse::stop();
        }

        return $this->gateway->handleCommand($message, $command, $parameter);
    }

    public function handleCallbackQuery(TelegramCallbackQuery $callbackQuery): FrontResponse
    {
        if ($this->fsmManager->processCallback($callbackQuery)) {
            return FrontResponse::stop();
        }

        return $this->gateway->handleCallbackQuery($callbackQuery);
    }

    public function handleBotChatStatusUpdate(TelegramUpdate $update): FrontResponse
    {
        if ($this->fsmManager->processStatusUpdate($update)) {
            return FrontResponse::stop();
        }

        return $this->gateway->handleBotChatStatusUpdate($update);
    }
}
