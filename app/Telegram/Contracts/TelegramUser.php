<?php 

namespace App\Telegram\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface TelegramUser extends Arrayable
{
    public function id(): int;
    public function username(): ?string;
    public function isBot(): bool;
    public function firstName(): string;
    public function lastName(): string;
    public function languageCode(): string;
    public function isPremium(): bool;

}