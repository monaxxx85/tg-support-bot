<?php

namespace App\Telegram\Formatters;

use App\Telegram\Contracts\TelegramFormatterInterface;
use App\Telegram\Presenters\BasePresenter;


class ViewFormatter implements TelegramFormatterInterface
{

    public function render(?BasePresenter $presenter = null, string $template = 'telegram.default'): string
    {
        $data = $presenter ? $presenter->toArray() : [];

        return view($template, $data);
    }
}
