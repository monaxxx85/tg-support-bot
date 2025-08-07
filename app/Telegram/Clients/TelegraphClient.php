<?php

namespace App\Telegram\Clients;

use App\Telegram\Contracts\TelegramClientInterface;
use DefStudio\Telegraph\DTO\Message;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Support\Facades\Log;

class TelegraphClient implements TelegramClientInterface
{

    protected bool $useQueue;

    public function __construct(bool $useQueue = null)
    {
        $this->useQueue = $useQueue ?? config('telegraph.use_queue', true);
    }

    /**
     * @param int $chatId
     * @param string $text
     * @param int|null $threadId
     * @return void
     * @throws \RuntimeException
     */
    public function sendMessage(int $chatId, string $text, ?int $threadId = null): void
    {
        $chat = Telegraph::chat($chatId);

        if ($threadId)
            $chat = $chat->inThread($threadId);

        if ($this->useQueue) {
            $chat->dispatch();

        } else {

            $response = $chat->message($text)->send();

            if (!$response->ok()) {
                Log::error("Telegram send failed", [
                    'chat_id' => $chatId,
                    'thread_id' => $threadId,
                    'error' => $response->json('description')
                ]);
                throw new \RuntimeException("Failed to send message: " . $response->json('description'));
            }
        }


    }

    public function copyMessage(int $toChatId, int $fromChatId, int $messageId, ?int $threadId = null): void
    {

        $chat = Telegraph::chat($toChatId)
            ->copyMessage($fromChatId, $messageId);


        if ($threadId)
            $chat = $chat->inThread($threadId);

        $response = $chat->send();

        Log::info('Ответ API:', ['response' => $response]);

        if (!$response->ok()) {
            throw new \RuntimeException(
                "Failed to forward message: " . $response->json('description')
            );
        }
    }


    public function createForumTopic(int $chatId, string $name, ?string $emoji = null): int
    {
        $response = Telegraph::chat($chatId)
            ->createForumTopic($name, null, $emoji)
            ->send();

        if (!$response->ok()) {
            Log::error("Topic creation failed", [
                'chat_id' => $chatId,
                'name' => $name,
                'error' => $response->json('description')
            ]);
            throw new \RuntimeException("Failed to create topic: " . $response->json('description'));
        }

        return $response->json('result.message_thread_id');
    }

    public function editForumTopic(int $chatId, int $threadId, ?string $name, ?string $emoji = null): void
    {
        $response = Telegraph::chat($chatId)
            ->editForumTopic($threadId, $name, $emoji)
            ->send();

        if (!$response->ok()) {
            throw new \RuntimeException(
                "Failed to delete forum topic: " . $response->json('description')
            );
        }
    }

    public function deleteForumTopic(int $chatId, int $topicId): void
    {
        $response = Telegraph::chat($chatId)
            ->deleteForumTopic($topicId)
            ->send();

        if (!$response->ok()) {
            throw new \RuntimeException(
                "Failed to delete forum topic: " . $response->json('description')
            );
        }
    }
}
