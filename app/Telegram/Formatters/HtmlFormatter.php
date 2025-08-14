<?php

namespace App\Telegram\Formatters;

use App\Telegram\Presenters\BasePresenter;
use App\Telegram\Contracts\TelegramFormatterInterface;


class HtmlFormatter implements TelegramFormatterInterface
{
    public function render(BasePresenter $presenter): string
    {
        $data = $presenter->toArray();

        $lines = [];

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '✅' : '❌';
            }

            if ($value === null || $value === '') {
                $value = '<i>нет данных</i>';
            }

            $label = $this->humanizeKey($key);

            if(is_array($value)){

                $lines[] = "<b>{$label}:</b>";

                foreach ($value as $_key => $_value){
                    $_label = $this->humanizeKey($_key);
                    $lines[] = "<b> — {$_label}: {$_value}</b>";
                }

            }else{

                $lines[] = "<b>{$label}:</b> {$value}";
            }

        }

        return implode("\n", $lines);
    }

    private function humanizeKey(string $key): string
    {
        return ucfirst(str_replace('_', ' ', $key));
    }
}
