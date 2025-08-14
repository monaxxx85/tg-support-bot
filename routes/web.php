<?php

use App\Telegram\Repository\SupportChatSessionRepository;
use App\Telegram\Enum\ChatStatusEmojiMapper;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Clients\TelegraphClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use  App\Telegram\DTO\TelegramConfig;

Route::get('/', function () {
    return view('welcome');
});
