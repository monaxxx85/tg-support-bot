<?php

namespace App\Telegram\Presenters;

use DefStudio\Telegraph\DTO\User;
use App\Telegram\Presenters\BasePresenter;

class ErrorPresenter extends BasePresenter
{
    public function __construct(private readonly \Throwable $exception)
    {}

    public function toArray(): array
    {
        return [
            'message' => $this->exception->getMessage(),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
        ];
    }
}
