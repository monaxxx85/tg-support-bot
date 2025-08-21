<?php 

namespace App\Telegram\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use  Illuminate\Support\Collection;

interface TelegramCallbackQuery extends Arrayable
{
    public function id(): ?int;
    public function from(): ?TelegramUser;
    public function message(): ?TelegramMessage;
    public function data(): ?Collection;
    
}