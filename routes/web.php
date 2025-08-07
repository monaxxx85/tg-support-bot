<?php

use App\Telegram\Repository\SupportChatSessionRepository;
use Illuminate\Support\Facades\Route;

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
