<?php

namespace App\Telegram\Webhook;

use App\Telegram\Contracts\WebhookDataInterface;
use Illuminate\Http\Request;

class WebhookData implements WebhookDataInterface
{
    public function __construct(
        protected Request $request
    ) {}

    public function getUserId(): ?int
    {
        return $this->request->input('message.from.id')
            ?? $this->request->input('callback_query.from.id')
            ?? null;
    }

    public function getChatId(): ?int
    {
        return $this->request->input('message.chat.id')
            ?? $this->request->input('callback_query.message.chat.id')
            ?? null;
    }

    public function getMessageId(): ?int
    {
        return $this->request->input('message.message_id')
            ?? $this->request->input('callback_query.message.message_id')
            ?? null;
    }

    public function isAdmin(): bool
    {
        return (string) $this->getUserId() === config('support.bot.owner_id');
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}