<?php

namespace App\Telegram;

use App\Models\UserTopic;
use DefStudio\Telegraph\DTO\Chat;
use DefStudio\Telegraph\DTO\Message;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\EmptyWebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;

class Handler extends EmptyWebhookHandler
{

    const TOPIC_ICON_NEW = '❓'; // Новый вопрос
    const TOPIC_ICON_ANSWERED = '✅'; // Ответ получен
    const TOPIC_ICON_DEFAULT = '💬'; // Обычный чат

    public function handle(Request $request, TelegraphBot $bot): void
    {
        parent::handle($request, $bot);
    }


    protected function handleChatMessage(Stringable $text): void
    {
        // все приватные сообщения
        if ($this->message?->chat()?->type() === Chat::TYPE_PRIVATE) {
            $this->handlePrivateMessage($text);

        } elseif ( // ответ из чата поддержки
            $this->message?->replyToMessage() &&
            $this->message->chat()->id() == env('TELEGRAM_SUPPORT_GROUP_ID')
        ) {
            $this->handleSupportReply($text);
        }

    }


    protected function handlePrivateMessage(Stringable $text): void
    {
        $topicId = $this->getTopicIdByMessage($this->message);

        Telegraph::message($text->value())
            ->inThread($topicId)
            ->send();

    }

    private function handleSupportReply(Stringable $text)
    {
        $userId = $this->getUserIdByMessage($this->message);

        if ($userId) {
            Telegraph::chat($userId)
                ->message($text->value())->send();
        }

    }

    protected function getTopicIdByMessage(Message $message): int
    {
        $userId = $message->from()->id();

        $userTopic = UserTopic::query()->where('user_id', '=', $userId)
            ->firstOr(function () use ($userId, $message) {

                $nameTopic = "ID: {$userId}";
                $response = Telegraph::createForumTopic($nameTopic)->send();
                $topicId = $response->json('result.message_thread_id');

                $userInfo = [
                    "👤 " . $this->message->from()->firstName() .' '. $this->message->from()->lastName() ,
                    "📱 @" . ($this->message->from()->username() ?? ' нет username'),
                    "🆔 $userId",
                ];

                Telegraph::message(implode("\n", $userInfo))
                    ->inThread($topicId)
                    ->send();

                return UserTopic::create([
                    'user_id' => $userId,
                    'thread_id' => $topicId,
                    'username' => $message->from()?->username(),
                    'first_name' => $message->from()?->firstName(),
                    'last_name' => $message->from()?->lastName(),
                ]);
            });

        return $userTopic->thread_id;
    }

    private function getUserIdByMessage(Message $message): int|null
    {
        $topicId = $message->replyToMessage()?->messageThreadId();
        if (!$topicId) {
            return NULL;
        }
        $userTopic = UserTopic::query()->where('thread_id', '=', $topicId)->first();
        return $userTopic->user_id;
    }

}
