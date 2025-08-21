<?php

namespace App\Telegram\Clients;

use App\Telegram\Contracts\TelegramClientInterface;
use Illuminate\Support\Facades\Log;

class LoggingTelegramClient implements TelegramClientInterface
{
    public function __construct(
        protected readonly TelegramClientInterface $client,
        protected readonly string $context = 'telegram'
    ) {
    }

    public function setQueue(bool $queue): TelegramClientInterface
    {
        $this->client->setQueue($queue);
        return $this;
    }

    public function sendMessage(int $chatId, string $text, ?int $threadId = null, ?array $keyboard = null): void
    {
        Log::info("Sending message to Telegram", [
            'context' => $this->context,
            'chat_id' => $chatId,
            'thread_id' => $threadId,
            'text_sample' => substr($text, 0, 50) . (strlen($text) > 50 ? '...' : '')
        ]);

        try {
            $this->client->sendMessage($chatId, $text, $threadId,$keyboard);
            Log::info("Message sent successfully", ['context' => $this->context]);
        } catch (\Exception $e) {
            Log::error("Failed to send message", [
                'context' => $this->context,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function copyMessage(int $toChatId, int $fromChatId, int $messageId, ?int $threadId = null): void
    {
        Log::info("Copying message ", [
            'context' => $this->context,
            'to_chat_id' => $toChatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
            'thread_id' => $threadId,
        ]);

        try {
            $this->client->copyMessage($toChatId, $fromChatId, $messageId, $threadId);
        } catch (\Exception $e) {
            Log::error("Failed to copying message", [
                'context' => $this->context,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function createForumTopic(int $chatId, string $name, ?string $emoji = null): int
    {
        Log::info("Creating Telegram topic", [
            'context' => $this->context,
            'chat_id' => $chatId,
            'name' => $name,
            'emoji' => $emoji
        ]);

        try {
            $topicId = $this->client->createForumTopic($chatId, $name, $emoji);
            Log::info("Topic created", [
                'context' => $this->context,
                'topic_id' => $topicId
            ]);
            return $topicId;
        } catch (\Exception $e) {
            Log::error("Failed to create topic", [
                'context' => $this->context,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteForumTopic(int $chatId, int $threadId): void
    {
        Log::info("Delete forum topic", [
            'context' => $this->context,
            'chatId' => $chatId,
            'threadId' => $threadId,
        ]);

        try {
            $this->client->deleteForumTopic($chatId, $threadId);
            Log::info("Delete forum topic end", [
                'context' => $this->context
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to delete forum topic", [
                'context' => $this->context,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function editForumTopic(int $chatId, int $threadId, ?string $name, ?string $emoji = null): void
    {

        Log::info("Edit forum topic", [
            'context' => $this->context,
            'chatId' => $chatId,
            'threadId' => $threadId,
            'name' => $name,
            'emoji' => $emoji,
        ]);


        try {
            $this->client->editForumTopic($chatId, $threadId, $name, $emoji);
            Log::info("Edit forum topic end", [
                'context' => $this->context
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to edit forum topic", [
                'context' => $this->context,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
