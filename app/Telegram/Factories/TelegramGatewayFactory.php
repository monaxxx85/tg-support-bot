<?php

namespace App\Telegram\Factories;

use App\Telegram\Contracts\WebhookDataInterface;
use App\Telegram\Gateways\TelegraphWebhookGateway;

class TelegramGatewayFactory
{
    public function __construct(
        protected TelegraphWebhookGateway $gatewayPrototype
    ) {}

    public function createFromWebhook(WebhookDataInterface $webhookData): TelegraphWebhookGateway
    {
        $gateway = clone $this->gatewayPrototype;
        $gateway->initialize($webhookData);
        return $gateway;
    }
}