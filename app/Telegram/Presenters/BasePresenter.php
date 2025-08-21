<?php

namespace App\Telegram\Presenters;

use Illuminate\Contracts\Support\Arrayable;

abstract class BasePresenter implements Arrayable
{
    abstract public function toArray(): array;
}
