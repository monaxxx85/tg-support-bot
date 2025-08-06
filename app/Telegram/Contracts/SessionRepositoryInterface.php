<?php

namespace App\Telegram\Contracts;

use App\Telegram\DTO\ChatSession;
use DefStudio\Telegraph\DTO\Message;

interface SessionRepositoryInterface
{
    /**
     * Находит активную сессию по ID пользователя
     */
    public function findByUser(int $telegramUserId): ?ChatSession;

    /**
     * Находит активную сессию по ID темы
     */
    public function findByTopic(int $topicMessageId): ?ChatSession;

    /**
     * Создает новую сессию на основе входящего сообщения
     */
    public function createSession(Message $message): ChatSession;

    /**
     * Сохраняет сессию в хранилище
     */
    public function saveSession(ChatSession $session): void;

    /**
     * Полностью удаляет сессию из хранилища
     */
    public function deleteSession(int $userId): void;

    /**
     * Возвращает все активные сессии
     */
    public function getAllActiveSessions(): array;

    /**
     * Помечает сессию как требующую очистки (при ошибке удаления темы)
     */
    public function markForCleanup(ChatSession $session): void;

    /**
     * Возвращает все сессии, ожидающие очистки
     */
    public function getPendingCleanupSessions(): array;

    /**
     * Удаляет сессию из списка ожидающих очистки
     */
    public function removePendingCleanup(int $topicId): void;
}
