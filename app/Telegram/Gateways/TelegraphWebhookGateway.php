<?php

namespace App\Telegram\Gateways;

use App\Telegram\Commands\CommandRouter;
use App\Telegram\Contracts\MessageTypeResolverInterface;
use App\Telegram\Contracts\SupportChatInterface;
use App\Telegram\Contracts\TelegramCallbackQuery;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Contracts\TelegramUpdate;
use App\Telegram\Contracts\TelegramWebhookGatewayInterface;
use App\Telegram\Contracts\WebhookDataInterface;
use App\Telegram\DTO\FrontResponse;
use App\Telegram\Facades\TelegramAuth;
use Illuminate\Http\Request;
use App\Telegram\Resolvers\ChatMemberUpdateResolver;
use App\Telegram\Services\ChatStatusService;


class TelegraphWebhookGateway implements TelegramWebhookGatewayInterface
{
    protected Request $request;
    private ?int $userId = null;
    private ?int $chatId = null;
    private ?int $messageId = null;
    private bool $isAdmin = false;

    public function __construct(
        protected MessageTypeResolverInterface $resolver,
        protected SupportChatInterface $chatService,
        protected CommandRouter $commandRouter,
        protected ChatMemberUpdateResolver $chatMemberUpdateResolver,
        protected ChatStatusService $chatStatusService,

    ) {}

    public function initialize(WebhookDataInterface $webhookData): void
    {
        $this->request = $webhookData->getRequest();
        $this->userId = $webhookData->getUserId();
        $this->chatId = $webhookData->getChatId();
        $this->messageId = $webhookData->getMessageId();
        $this->isAdmin = $webhookData->isAdmin();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getChatId(): int
    {
       return $this->chatId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function handleChatMessage(TelegramMessage $message): FrontResponse
    {
        // системные сообщения
        $rawMessage = $this->request->input('message', []);
        if ($this->resolver->isSystemMessageArray($rawMessage)) {
            return FrontResponse::next();
        }

        if (!$this->chatId) {
            return FrontResponse::next();
        }

        // пользовательские сообщения
        match (true) {
            $this->resolver->isPrivate($message)
            => $this->chatService->handleUserMessage($message),

            $this->resolver->isReplyInTopic($message)
            => $this->chatService->handleSupportReply($message),

            default => null
        };

        return FrontResponse::next();
    }

    public function handleCommand(TelegramMessage $message, string $command, string $parameter): FrontResponse
    {
        /**
         * @var TelegramCommandInterface|null
         */
        $commandHandler = $this->commandRouter->resolve($message, $command);
        
        if ($commandHandler !== null) {
            $commandHandler->execute($message, $parameter);
            return FrontResponse::stop();
        }

        return FrontResponse::next();
    }

    public function handleCallbackQuery(TelegramCallbackQuery $callbackQuery): FrontResponse
    {
        // нет обработки кроме сценариев 
        return FrontResponse::next();
    }

    public function handleBotChatStatusUpdate(TelegramUpdate $update): FrontResponse
    {
        // Пользователь заблокировал бота в ЛС
        if ($this->chatMemberUpdateResolver->isUserBannedBot($update)) {
            $this->chatStatusService
                ->markAsBannedByUser($update->from()->id());
            return FrontResponse::stop();
        }

        // Пользователь вступил в группу
        if ($this->chatMemberUpdateResolver->isUserJoinedGroup($update)) {
            $this->chatStatusService
                ->updateStatus($update->from()->id(), ChatStatus::NEW);

            return FrontResponse::stop();
        }

        // Пользователь покинул или был кикнут
        if ($this->chatMemberUpdateResolver->isUserLeftGroup($update)) {

            return FrontResponse::stop();
        }


        return FrontResponse::next();
    }
}
