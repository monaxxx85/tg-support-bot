<?php


return [
    'fsm' => [
        'scenarios' => [
            'registration' => App\Telegram\FSM\Scenarios\Registration\RegistrationScenario::class
        ]
    ],
    'command'=>[
        'private'=>[
            \App\Telegram\Commands\PrivateChat\StartCommand::class,
            \App\Telegram\Commands\PrivateChat\RegCommand::class
        ],
        'support'=>[
            \App\Telegram\Commands\SupportChat\ContactCommand::class
        ],
        'general'=>[
            \App\Telegram\Commands\GeneralChat\TestCommand::class
        ],
    ]

];
