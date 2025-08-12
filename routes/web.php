<?php

use App\Telegram\Repository\SupportChatSessionRepository;
use App\Telegram\Enum\ChatStatusEmojiMapper;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Clients\TelegraphClient;
use Illuminate\Support\Facades\Route;
use  App\Telegram\DTO\TelegramConfig;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/all', function () {
    $store = new SupportChatSessionRepository();
    dd($store->getAllActiveSessions());
});

Route::get('/delete/{id}', function ($id) {
    $store = new SupportChatSessionRepository();
    $store->deleteSession($id);

});

Route::get('/new/{id?}', function (?string $id = null) {

    $groupId = env('TELEGRAM_SUPPORT_GROUP_ID');
    if ($id) {
        $emId = $id;
    } else {
      /**
         * @var TelegramEmoji
         */
        $emoji = ChatStatusEmojiMapper::getEmoji(ChatStatus::NEW);

        $emId = $emoji->value();
    }


    $client = new TelegraphClient(false);
    echo $client->createForumTopic($groupId, 'new topic', $emId);


});
