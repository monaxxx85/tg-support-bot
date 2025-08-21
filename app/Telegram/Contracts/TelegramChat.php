<?php

namespace App\Telegram\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface TelegramChat extends Arrayable
{
    public const TYPE_SENDER = 'sender';
    public const TYPE_PRIVATE = 'private';
    public const TYPE_GROUP = 'group';
    public const TYPE_SUPERGROUP = 'supergroup';
    public const TYPE_CHANNEL = 'channel';

    public function id(): string;
    public function type(): string;
    public function title(): string;
}
