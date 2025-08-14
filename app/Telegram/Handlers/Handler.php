<?php

namespace App\Telegram\Handlers;

use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\SupportChatInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\FSM\Core\Event;
use App\Telegram\FSM\Core\FSM;
use App\Telegram\Resolvers\ChatMemberUpdateResolver;
use App\Telegram\Services\ChatStatusService;
use DefStudio\Telegraph\DTO\ChatMemberUpdate;
use DefStudio\Telegraph\Exceptions\TelegramWebhookException;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use App\Telegram\Commands\CommandRouter;
use App\Telegram\Contracts\TelegramCommandInterface;

class Handler extends WebhookHandler
{
    public function __construct(
        protected MessageTypeResolverInterface $resolver,
        protected SupportChatInterface         $chatService,
        protected TelegramClientInterface      $telegramClient,
        protected CommandRouter                $commandRouter,
        protected ChatMemberUpdateResolver     $chatMemberUpdateResolver,
        protected ChatStatusService            $chatStatusService,
        protected FSM                          $fsm
    )
    {
        parent::__construct();
    }

    public function test($var1, $var2)
    {
        // test alert in telegram.
        $this->reply("Notification run: var1=$var1 ; var2=$var2 ");

        // or action ...

    }

    protected function handleBotChatStatusUpdate(ChatMemberUpdate $chatMemberUpdate): void
    {

        // сценарий
        $isOn = $this->fsm->dispatch(
            new Event(
                'update',
                [
                    'chat_id' => $chatMemberUpdate->chat()->id(),
                    'from_id' => $chatMemberUpdate->from()->id(),
                    'previous_status' => $chatMemberUpdate->previous()->status(),
                    'new_status' => $chatMemberUpdate->new()->status(),
                ]
            ),
            $chatMemberUpdate->from()->id(),
            $chatMemberUpdate->chat()->id(),
        );

        if ($isOn) {
            return;
        }


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

    protected function handleCallbackQuery(): void
    {
        $this->extractCallbackQueryData();

        if (config('telegraph.debug_mode', config('telegraph.webhook.debug'))) {
            Log::debug('Telegraph webhook callback', $this->data->toArray());
        }

        // сценарий
        $isOn = $this->fsm->dispatch(
            new Event(
                'callback',
                $this->data->toArray() ?? []
            ),
            $this->callbackQuery->from()->id(),
            $this->chat->chat_id

        );

        if ($isOn) {
            return;
        }


        /** @var string $action */
        $action = $this->callbackQuery?->data()->get('action') ?? '';

        if (!$this->canHandle($action)) {
            report(TelegramWebhookException::invalidAction($action));
            $this->reply(__('telegraph::errors.invalid_action'));

            return;
        }

        /** @phpstan-ignore-next-line */
        App::call([$this, $action], $this->data->toArray());
    }


    protected function handleCommand(Stringable $text): void
    {
        [$command, $parameter] = $this->parseCommand($text);


        // сценарий
        $isOn = $this->fsm->dispatch(
            new Event(
                'command',
                [
                    'name' => $command,
                    'parameter' => $parameter
                ]
            ),
            $this->message->from()->id(),
            $this->message->chat()->id(),
        );

        if ($isOn) {
            return;
        }


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

        // системные сообщения
        $rawMessage = $this->request->input('message', []);
        if ($this->resolver->isSystemMessageArray($rawMessage)) {
            return;
        }


        // сценарий
        $isOn = $this->fsm->dispatch(
            new Event(
                'text',
                ['text' => $text->value()]
            ),
            $this->message->from()->id(),
            $this->message->chat()->id()
        );

        if ($isOn) {
            return;
        }


        // стандартная логика
        match (true) {
            $this->resolver->isPrivate($this->message) =>
            $this->chatService->handleUserMessage($this->message),

            $this->resolver->isReplyInTopic($this->message) =>
            $this->chatService->handleSupportReply($this->message),

            default => null
        };
    }


}
