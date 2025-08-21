<?php

namespace App\Telegram\Contracts;

use Illuminate\Http\Request;

interface WebhookDataInterface
{
    public function getUserId(): ?int;
    public function getChatId(): ?int;
    public function getMessageId(): ?int;
    public function isAdmin(): bool;
    public function getRequest(): Request;
}