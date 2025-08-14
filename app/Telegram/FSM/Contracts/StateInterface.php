<?php

namespace App\Telegram\FSM\Contracts;

use App\Telegram\FSM\Core\Context;
use App\Telegram\FSM\Core\Event;
use App\Telegram\FSM\Core\StateId;

interface StateInterface
{
    public function id(): StateId;

    /** Вызывается при входе в состояние */
    public function onEnter(Context $ctx): void;

    /** Обработка события; вернуть следующий StateId или null, если остаёмся */
    public function handle(Event $event, Context $ctx): ?StateId;
}
