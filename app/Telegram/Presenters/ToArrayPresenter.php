<?php

namespace App\Telegram\Presenters;

use Illuminate\Contracts\Support\Arrayable;

class ToArrayPresenter extends BasePresenter
{
    public function __construct(protected Arrayable $arrayable)
    {}

    public function toArray(): array
    {
        return  $this->arrayable->toArray();
    }
}
