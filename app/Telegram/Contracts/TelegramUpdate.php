<?php

namespace App\Telegram\Contracts;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;

interface TelegramUpdate extends Arrayable
{

    public function date(): ?CarbonInterface;
    public function from(): ?TelegramUser;
    public function chat(): ?TelegramChat;
    public function previous(): ?TelegramChatMember;
    public function new(): ?TelegramChatMember;
}
