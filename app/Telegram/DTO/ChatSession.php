<?php

namespace App\Telegram\DTO;


class UserSession
{

    private int $id;
    private int $topicId;

    private bool $isBot;
    private string $firstName;
    private string $lastName;
    private string $username;
    private string $languageCode;
    private bool $isPremium;


    /**
     * @param array{id:int, is_bot?:bool, first_name?:string, last_name?:string, username?:string, language_code?:string, is_premium?:bool} $data
     */
    public static function fromArray(array $data): UserSession
    {
        $user = new self();

        $user->topicId = $data['topicId'];

        $user->id = $data['id'];
        $user->isBot = $data['is_bot'] ?? false;

        $user->firstName = $data['first_name'] ?? '';
        $user->lastName = $data['last_name'] ?? '';
        $user->username = $data['username'] ?? '';
        $user->languageCode = $data['language_code'] ?? '';
        $user->isPremium = $data['is_premium'] ?? false;

        return $user;
    }
}


