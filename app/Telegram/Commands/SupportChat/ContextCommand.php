<?php

namespace App\Telegram\Commands\SupportChat;


use App\Telegram\Contracts\TelegramClientInterface;
use App\Telegram\DTO\TelegramConfig;
use App\Telegram\Formatters\HtmlFormatter;
use App\Telegram\FSM\Contracts\ContextRepositoryInterface;
use App\Telegram\Contracts\TelegramMessage;
use App\Telegram\Commands\BaseCommand;
use App\Telegram\Contracts\SessionRepositoryInterface;
use App\Telegram\Presenters\ToArrayPresenter;


class ContextCommand extends BaseCommand
{
    protected int $supportChatId;

    public function __construct(
        protected SessionRepositoryInterface $sessionRepository,
        protected ContextRepositoryInterface $contextRepository,
        protected TelegramClientInterface    $telegramClient,
        protected TelegramConfig             $config,
    )
    {
    }

    public function getName(): string
    {
        return 'context';
    }

    public function getAliases(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return "Получить данные о текущем состоянии сценария у пользователя /context {parameter?}";
    }

    public function execute(TelegramMessage $message, ?string $parameter = null): void
    {
        $topicId = $message->messageThreadId();
        if (!$topicId)
            return;

        $session = $this->sessionRepository->findByTopic($topicId);

        if (!$session)
            return;


        if ($parameter === "") {

            $context = $this->contextRepository->load((int)$session->telegram_user_id);

            $this->telegramClient
                ->sendMessage(
                    $this->config->supportGroupId,
                    (new HtmlFormatter())->render(new ToArrayPresenter($context)),
                    $topicId
                );

        } elseif($parameter === "clear") {

            $this->contextRepository->reset((int)$session->telegram_user_id);

            $this->telegramClient
                ->sendMessage(
                    $this->config->supportGroupId,
                    'Готово',
                    $topicId
                );
        }


    }
}
