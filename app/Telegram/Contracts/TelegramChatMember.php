<?php

namespace App\Telegram\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface TelegramChatMember extends Arrayable
{
    public const STATUS_CREATOR = 'creator';
    public const STATUS_ADMINISTRATOR = 'administrator';
    public const STATUS_MEMBER = 'member';
    public const STATUS_RESTRICTED = 'restricted';
    public const STATUS_LEFT = 'left';
    public const STATUS_KICKED = 'kicked';

    public function status(): string;
    public function user(): ?TelegramUser;
    public function isAnonymous(): bool;
    public function custom_title(): string;
    public function is_member(): bool;
    public function until_date(): ?int;
}
