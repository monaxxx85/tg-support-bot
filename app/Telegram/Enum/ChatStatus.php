<?php

namespace App\Telegram\Enum;

enum ChatStatus: string
{
    case NEW = 'new';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case PENDING = 'pending';
    case BANNED = 'banned';
}
