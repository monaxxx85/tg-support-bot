<?php

namespace App\Telegram\FSM\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

final class Context implements Arrayable
{
    public function __construct(
        public int      $userId,
        public ?int     $chatId,
        public ?StateId $state = null,
        public array    $bag = [],
        public ?string  $ttlAt = null,
        public bool     $isAsyncPending = false,
        public ?string  $asyncJobId = null,
    ){}

    public function isExpired(): bool
    {
        return $this->ttlAt !== null
            && Carbon::now()->gt(Carbon::parse($this->ttlAt));
    }

    public function setAsyncPending(string $jobId): void
    {
        $this->isAsyncPending = true;
        $this->asyncJobId = $jobId;
    }

    public function clearAsyncPending(): void
    {
        $this->isAsyncPending = false;
        $this->asyncJobId = null;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'chat_id' => $this->chatId,
            'state' => $this->state?->key(),
            'bag' => $this->bag ?? [],
            'ttl_at' => $this->ttlAt,
            'is_async_pending' => $this->isAsyncPending ?? false,
            'async_job_id' => $this->asyncJobId ?? '',
        ];
    }

}
