<?php

namespace App\Telegram\Handlers;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\SupportChatInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Resolvers\ChatMemberUpdateResolver;
use App\Telegram\Services\ChatStatusService;
use DefStudio\Telegraph\DTO\ChatMemberUpdate;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;
use App\Telegram\Commands\CommandRouter;
use App\Telegram\Contracts\TelegramCommandInterface;

class Handler extends WebhookHandler
{
    public function __construct(
        protected MessageTypeResolverInterface $resolver,
        protected SupportChatInterface $chatService,
        protected TelegramClientInterface $telegramClient,
        protected CommandRouter $commandRouter,
        protected ChatMemberUpdateResolver $chatMemberUpdateResolver,
        protected ChatStatusService $chatStatusService,
    ) {
        parent::__construct();
    }

    public function test($var1,$var2)
    {
        // test alert in telegram.
        $this->reply("Notification run: var1=$var1 ; var2=$var2 ");

        // or action ...

    }

    protected function handleBotChatStatusUpdate(ChatMemberUpdate $chatMemberUpdate): void
    {

        // Пользователь заблокировал бота в ЛС
        if ($this->chatMemberUpdateResolver->isUserBannedBot($chatMemberUpdate)) {
            $this->chatStatusService
                ->markAsBannedByUser($chatMemberUpdate->from()->id());

            return;
        }

        // Пользователь вступил в группу
        if ($this->chatMemberUpdateResolver->isUserJoinedGroup($chatMemberUpdate)) {
            $this->chatStatusService
                ->updateStatus($chatMemberUpdate->from()->id(), ChatStatus::NEW);

            return;
        }

        // Пользователь покинул или был кикнут
        if ($this->chatMemberUpdateResolver->isUserLeftGroup($chatMemberUpdate)) {

            return;
        }

    }


    protected function handleCommand(Stringable $text): void
    {
        [$command, $parameter] = $this->parseCommand($text);

        /**
         * @var TelegramCommandInterface|null
         */
        $commandHandler = $this->commandRouter->resolve($this->message, $command);

        if ($commandHandler) {
            $commandHandler->execute($this->message, $parameter);
            return;
        }


        if (!$this->canHandle($command)) {
            $this->handleUnknownCommand($text);

            return;
        }

        $this->$command($parameter);
    }


    protected function handleChatMessage(Stringable $text): void
    {
        $rawMessage = $this->request->input('message', []);

        if ($this->resolver->isSystemMessageArray($rawMessage)) {
            return;
        }

        match (true) {
            $this->resolver->isPrivate($this->message) =>
            $this->chatService->handleUserMessage($this->message),

            $this->resolver->isReplyInTopic($this->message) =>
            $this->chatService->handleSupportReply($this->message),

            default => null
        };
    }


}
