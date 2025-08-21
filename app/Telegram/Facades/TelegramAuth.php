<?php

namespace App\Telegram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void init(int $userId, int $chatId, bool $isAdmin = false, ?int $messageId = null)
 * @method static int|null userId()
 * @method static int|null chatId()
 * @method static int|null messageId()
 * @method static bool isAdmin()
 * @method static int sessionDuration()
 * @method static bool isAuthenticated()
 * @method static void reset()
 * @method static array toArray()
 *
 * @see \App\Telegram\Services\TelegramAuth
 */
class TelegramAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Telegram\Services\TelegramAuth::class;
    }
}