<?php

namespace App\Telegram\Contracts;

use App\Telegram\Presenters\BasePresenter;

interface TelegramFormatterInterface
{
    public function render(BasePresenter $presenter): string;
}
