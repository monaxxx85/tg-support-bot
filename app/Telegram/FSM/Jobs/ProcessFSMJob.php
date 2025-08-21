<?php

namespace App\Telegram\FSM\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Telegram\FSM\Core\FSM;
use App\Telegram\FSM\Core\Event;

class ProcessFSMJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, Dispatchable;

    public function __construct(
        private int $userId,
        private int $chatId,
        private Event $event
    ) {}

    public function handle(FSM $fsm): void
    {
        try {
            $fsm->dispatch($this->event, $this->userId, $this->chatId);
        } catch (\Exception $e) {
            \Log::error('FSM Jobs failed', [
                'user_id' => $this->userId,
                'event' => $this->event->toArray(),
                'error' => $e->getMessage()
            ]);

            $this->fail($e);
        }
    }
}
