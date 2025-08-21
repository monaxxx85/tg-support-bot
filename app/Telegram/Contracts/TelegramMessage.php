<?php 

namespace App\Telegram\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface TelegramMessage extends Arrayable
{
    public function from(): ?TelegramUser;
    public function chat(): ?TelegramChat;
    public function replyToMessage(): ?TelegramMessage;
    public function id(): ?int;
    public function text(): ?string;
    public function messageThreadId(): ?int;
}