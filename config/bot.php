<?php


return [
    'fsm' => [
        'scenarios' => [
            'registration' => App\Telegram\FSM\Scenarios\Registration\RegistrationScenario::class,
            'get_contact' => App\Telegram\FSM\Scenarios\GetContact\GetContactScenario::class,
        ]
    ],
    'command'=>[
        'private'=>[
            \App\Telegram\Commands\PrivateChat\StartCommand::class,
            \App\Telegram\Commands\PrivateChat\RegCommand::class
        ],
        'support'=>[
            \App\Telegram\Commands\SupportChat\ContactCommand::class,
            \App\Telegram\Commands\SupportChat\GetContactCommand::class,
            \App\Telegram\Commands\SupportChat\InfoSessionCommand::class
        ],
        'general'=>[
            \App\Telegram\Commands\GeneralChat\TestCommand::class
        ],
    ]

];
