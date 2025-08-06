<?php

namespace App\Telegram\Presenters;

use DefStudio\Telegraph\DTO\User;

class UserPresenter
{
    public function formatTopicName(User $user): string
    {
        return match (true) {
            !empty($user->firstName() && $user->lastName()) => "{$user->firstName()} {$user->lastName()}",
            !empty($user->username()) => "@{$user->username()}",
            default => "ID: " . (string)$user->id()
        };
    }

    public function contact(User $user): string
    {
        $userInfo = [
            "👤 {$user->firstName()} {$user->lastName()}",
            "📱 @" . ($user->username() ?? ' нет username'),
            "🆔 {$user->id()}",
            "Is bot = " . $user->isBot() ? "true" : "false"
        ];

        return implode("\n", $userInfo);
    }

}
