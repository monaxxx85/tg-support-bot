<?php

namespace App\Telegram\Handlers;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Services\SupportChatService;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\EmptyWebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;

class Handler extends EmptyWebhookHandler
{
    public function __construct(
        protected MessageTypeResolverInterface $resolver,
        protected SupportChatService           $chatService,
        protected TelegramClientInterface      $telegramClient,
    )
    {
        parent::__construct();
    }


    public function handle(Request $request, TelegraphBot $bot): void
    {
        parent::handle($request, $bot);
    }


    protected function handleChatMessage(Stringable $text): void
    {
        if ($text->isEmpty()) {
            return;
        }

        match (true) {
            $this->resolver->isPrivate($this->message) => $this->handlePrivateMessage(),
            $this->resolver->isReplyInTopic($this->message) => $this->handleSupportReply(),
            default => null
        };

    }


    protected function handlePrivateMessage(): void
    {
        $this->chatService->handleUserMessage($this->message);
    }


    private function handleSupportReply(): void
    {
        $this->chatService->handleSupportReply($this->message);
    }

}
