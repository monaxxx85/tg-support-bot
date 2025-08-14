<?php

namespace App\Telegram\Presenters;

use App\Telegram\DTO\ChatSession;

class SessionPresenter extends BasePresenter
{
    public function __construct(protected ChatSession $session)
    {}

    public function toArray(): array
    {
        return $this->session->toArray();
//        return [
//            'message' => $this->exception->getMessage(),
//            'file' => $this->exception->getFile(),
//            'line' => $this->exception->getLine(),
//        ];
    }
}
