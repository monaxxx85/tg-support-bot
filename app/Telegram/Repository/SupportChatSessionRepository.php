<?php

namespace App\Telegram\Repository;

use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\DTO\ChatSession;
use App\Telegram\Enum\ChatStatus;
use DefStudio\Telegraph\Concerns\HasStorage;
use DefStudio\Telegraph\Contracts\Storable;
use DefStudio\Telegraph\DTO\Message;
use Illuminate\Support\Facades\Log;


class SupportChatSessionRepository implements SessionRepositoryInterface, Storable
{

    use HasStorage;

    // Префиксы для ключей хранилища
    private const USER_PREFIX = 'support:user:';
    private const TOPIC_PREFIX = 'support:topic:';
    private const ALL_SESSIONS_KEY = 'support:all_sessions';
    private const PENDING_CLEANUP_KEY = 'support:pending_cleanup';


    public function findByUser(int $telegramUserId): ?ChatSession
    {
        $sessionData = $this->storage()->get(self::USER_PREFIX . $telegramUserId);

        // Возвращаем null, если сессия не найдена или закрыта
        if (!$sessionData || ($sessionData['closed_at'] ?? null)) {
            return null;
        }

        return ChatSession::fromArray($sessionData);
    }

    public function findByTopic(int $topicMessageId): ?ChatSession
    {
        $sessionData = $this->storage()->get(self::TOPIC_PREFIX . $topicMessageId);

        // Возвращаем null, если сессия не найдена или закрыта
        if (!$sessionData || ($sessionData['closed_at'] ?? null)) {
            return null;
        }

        return ChatSession::fromArray($sessionData);
    }

    public function createSession(Message $message): ChatSession
    {
        $from = $message->from();

        return new ChatSession(
            status: ChatStatus::NEW ,
            telegram_user_id: $from->id(),
            topicId: 0, // будет установлен позже
            firstName: $from->firstName() ?? '',
            lastName: $from->lastName() ?? '',
            username: $from->username() ?? '',
            isBot: $from->isBot() ?? false,
            languageCode: $from->languageCode() ?? 'en',
            isPremium: $from->isPremium() ?? false,
            is_banned: false,
            last_message_from: 'user',
            created_at: now()->toIso8601String(),
            closed_at: null,
            closed_reason: null,
        );
    }

    public function saveSession(ChatSession $session): void
    {
        $sessionData = $session->toArray();

        // Сохраняем данные в транзакции для атомарности
        // $this->storage()->transaction(function () use ($session, $sessionData, $ttl) {


        // Сохраняем по ID пользователя
        $this->storage()->set(
            self::USER_PREFIX . $session->telegram_user_id,
            $sessionData
        );

        // Сохраняем по ID темы (если установлен)
        if ($session->topicId > 0) {
            $this->storage()->set(
                self::TOPIC_PREFIX . $session->topicId,
                $sessionData
            );
        }


        // });

        // Добавляем в список всех сессий
        $this->addToAllSessions($session->telegram_user_id);
    }

    public function deleteSession(int $userId): void
    {
        $session = $this->findByUser($userId);

        if (!$session) {
            return;
        }

        // Удаляем из основного хранилища
        $this->storage()->forget(self::USER_PREFIX . $userId);

        if ($session->topicId > 0) {
            $this->storage()->forget(self::TOPIC_PREFIX . $session->topicId);
        }

        // Удаляем из списка всех сессий
        $allSessions = $this->storage()->get(self::ALL_SESSIONS_KEY, []);
        $allSessions = array_filter($allSessions, fn($id) => $id != $userId);
        $this->storage()->set(self::ALL_SESSIONS_KEY, array_values($allSessions));
    }

    public function getAllActiveSessions(): array
    {
        $allSessions = $this->storage()->get(self::ALL_SESSIONS_KEY, []);
        $activeSessions = [];

        foreach ($allSessions as $userId) {
            $session = $this->findByUser($userId);
            if ($session) {
                $activeSessions[] = $session;
            }
        }

        return $activeSessions;
    }

    public function markForCleanup(ChatSession $session): void
    {
        $pending = $this->getPendingCleanupSessions();

        // Добавляем или обновляем запись
        $pending[$session->topicId] = [
            'session' => $session->toArray(),
            'attempts' => ($pending[$session->topicId]['attempts'] ?? 0) + 1,
            'last_attempt' => now()->toIso8601String(),
        ];

        $this->storage()->set(self::PENDING_CLEANUP_KEY, $pending);

        Log::warning("Session marked for cleanup", [
            'user_id' => $session->telegram_user_id,
            'topic_id' => $session->topicId,
            'attempts' => $pending[$session->topicId]['attempts']
        ]);
    }

    public function getPendingCleanupSessions(): array
    {
        return $this->storage()->get(self::PENDING_CLEANUP_KEY, []);
    }

    public function removePendingCleanup(int $topicId): void
    {
        $pending = $this->getPendingCleanupSessions();

        if (isset($pending[$topicId])) {
            unset($pending[$topicId]);
            $this->storage()->set(self::PENDING_CLEANUP_KEY, $pending);
        }
    }

    /**
     * Добавляет пользователя в список всех сессий
     */
    private function addToAllSessions(int $userId): void
    {
        $allSessions = $this->storage()->get(self::ALL_SESSIONS_KEY, []);

        if (!in_array($userId, $allSessions)) {
            $allSessions[] = $userId;
            $this->storage()->set(self::ALL_SESSIONS_KEY, $allSessions);
        }
    }

    public function storageKey(): string|int
    {
        return self::class;
    }
}
