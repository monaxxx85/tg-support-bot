<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\SupportChatInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Jobs\CreateTopicJob;
use App\Telegram\Enum\ChatStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Bus;

class AsyncSupportChatService implements SupportChatInterface
{
    protected int $supportGroupId;

    public function __construct(
        protected readonly SessionRepositoryInterface $sessionRepository,
        protected readonly TelegramClientInterface $telegramClient,
        protected readonly TelegramConfig $config,
        protected readonly ChatStatusService $chatStatusService
    ) {
        $this->supportGroupId = $this->config->supportGroupId;
    }

    public function handleUserMessage(TelegramMessage $message): void
    {
        $userId = $message->from()->id();
        $lockKey = $this->lockKey($userId);
        $bufferKey = $this->bufferKey($userId);

        $session = $this->sessionRepository->findByUser($userId);

        // Если нет сессии или топика, а джоба ещё не создаёт топик
        if (!$session || !$session->topicId) {
            if (!Cache::has($lockKey)) {
                // Ставим блокировку и запускаем джобу создания топика
                Cache::put($lockKey, true, 60);
                Cache::put($bufferKey, [], 300);

                Bus::dispatch(new CreateTopicJob($message, $this->supportGroupId));
            } else {
                // Сохраняем сообщение в буфер
                $buffer = Cache::get($bufferKey, []);
                $buffer[] = [
                    'chat_id' => $message->chat()->id(),
                    'message_id' => $message->id(),
                ];
                Cache::put($bufferKey, $buffer, 300);
            }
            return;
        }

        // Если топик есть — пересылаем сразу
        $this->telegramClient->copyMessage(
            $this->supportGroupId,
            $message->chat()->id(),
            $message->id(),
            $session->topicId
        );

        $session->last_message_from = 'user';
        $this->sessionRepository->saveSession($session);
    }

    public function handleSupportReply(object $message): void
    {
        $topicId = $message->replyToMessage()?->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        $this->telegramClient->copyMessage(
            $session->telegram_user_id,
            $message->chat()->id(),
            $message->id()
        );

        $session->last_message_from = 'support';
        $this->sessionRepository->saveSession($session);


        if ($session->status == ChatStatus::NEW) {
            $this->chatStatusService->updateStatus($session->telegram_user_id, ChatStatus::OPEN);
        }

    }

    protected function lockKey(int $userId): string
    {
        return "user_topic_lock:{$userId}";
    }

    protected function bufferKey(int $userId): string
    {
        return "user_topic_buffer:{$userId}";
    }
}
