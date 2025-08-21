<?php

namespace App\Telegram\Jobs;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Enum\ChatStatus;
use App\Telegram\Enum\ChatStatusEmojiMapper;
use App\Telegram\Formatters\ContactUserFormatter;
use App\Telegram\Presenters\UserPresenter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CreateTopicJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 15, 30];
    public $uniqueFor = 60;

    public function __construct(
        protected TelegramMessage $message,
        protected int $supportGroupId
        )
    {}

    public function uniqueId(): string
    {
        return 'create-topic-user-' . $this->message->from()->id();
    }

    public function handle(
        SessionRepositoryInterface $sessions,
        TelegramClientInterface $telegram
    ): void
    {
        $telegram->setQueue(false);
        $userId = $this->message->from()->id();
        $lockKey = $this->lockKey($userId);
        $bufferKey = $this->bufferKey($userId);

        $session = $sessions->findByUser($userId);

        if ($session && $session->topicId) {
            Cache::forget($lockKey);
            return;
        }

        try {
            // 1. Создаём топик
            $presenter = new UserPresenter($this->message->from());
            $topicName = $presenter->topicName();
            $emoji = ChatStatusEmojiMapper::getEmoji(ChatStatus::NEW);

            $topicId = $telegram->createForumTopic(
                $this->supportGroupId,
                $topicName,
                $emoji->value
            );

            // 2. Отправляем контакт
            $telegram->sendMessage(
                $this->supportGroupId,
                (new ContactUserFormatter)->render($presenter),
                $topicId
            );

            // 3. Отправляем первое сообщение
            $telegram->copyMessage(
                $this->supportGroupId,
                $this->message->chat()->id(),
                $this->message->id(),
                $topicId
            );

            // 4. Отправляем накопленные сообщения
            $buffer = Cache::get($bufferKey, []);
            foreach ($buffer as $msg) {
                $telegram->copyMessage(
                    $this->supportGroupId,
                    $msg['chat_id'],
                    $msg['message_id'],
                    $topicId
                );
            }

            // 5. Сохраняем сессию
            if (!$session) {
                $session = $sessions->createSession($this->message);
            }
            $session->topicId = $topicId;
            $sessions->saveSession($session);

            // 6. Снимаем блокировки и чистим буфер
            Cache::forget($lockKey);
            Cache::forget($bufferKey);

        } catch (Throwable $e) {
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
            Cache::forget($lockKey);
            Cache::forget($bufferKey);
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
