<?php

namespace App\Telegram\Formatters;

use App\Telegram\Presenters\BasePresenter;
use App\Telegram\Contracts\TelegramFormatterInterface;


class ContactUserFormatter implements TelegramFormatterInterface
{
    public function render(BasePresenter $presenter): string
    {
        $data = $presenter->toArray();

       return implode("\n", [
            "👤 <b>{$data['full_name']}</b>",
            "📱 " . ($data['username'] ? '@' . $data['username'] : '<i>нет username</i>'),
            "🆔 <code>{$data['id']}</code>",
            "🤖 " . ($data['is_bot'] ? "<b>бот</b>" : "человек"),
        ]);
    }

}
