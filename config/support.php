<?php

return [

    // общее настройки
    'bot' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'owner_id' => env('TELEGRAM_BOT_OWNER'),
        'support_group_id' => env('TELEGRAM_SUPPORT_GROUP_ID'),
        'use_queue' => env('TELEGRAM_USE_QUEUE', false),
    ],


    // регистрация сценариев
    'fsm' => [
        'scenarios' => [
            App\Telegram\FSM\Scenarios\Registration\RegistrationScenario::class,
            App\Telegram\FSM\Scenarios\GetContact\GetContactScenario::class,
        ]
    ],


    // регистрация команд
    'command' => [
        'private' => [
            \App\Telegram\Commands\PrivateChat\StartCommand::class,
            \App\Telegram\Commands\PrivateChat\RegCommand::class,
        ],
        'support' => [
            \App\Telegram\Commands\SupportChat\ContactCommand::class,
            \App\Telegram\Commands\SupportChat\GetPhoneNumberCommand::class,
            \App\Telegram\Commands\SupportChat\InfoSessionCommand::class,
            \App\Telegram\Commands\SupportChat\ContextCommand::class
        ],
        'general' => [
            \App\Telegram\Commands\GeneralChat\TestCommand::class
        ],
    ]

];
