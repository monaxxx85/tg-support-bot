<?php

namespace App\Telegram\Webhook;

use App\Telegram\Adapters\DefStudioCallbackQuery;
use App\Telegram\Adapters\DefStudioMessage;
use App\Telegram\Adapters\DefStudioUpdate;
use App\Telegram\TelegramFrontController;
use App\Telegram\Factories\TelegramGatewayFactory;
use DefStudio\Telegraph\DTO\ChatMemberUpdate;
use DefStudio\Telegraph\Exceptions\TelegramWebhookException;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use App\Telegram\Webhook\WebhookData;



class Handler extends WebhookHandler
{
    protected TelegramFrontController $front;

    public function __construct(
        protected TelegramFrontController $frontController,
        protected TelegramGatewayFactory $gatewayFactory
    ) {
        parent::__construct();
    }

    public function handle(Request $request, TelegraphBot $bot): void
    {
        $webhookData = new WebhookData($request);
        $gateway = $this->gatewayFactory->createFromWebhook($webhookData);
        $this->front = $this->frontController->withGateway($gateway);
        parent::handle($request, $bot);
    }

    public function test($var1, $var2)
    {
        // test alert in telegram.
        $this->reply("Notification run: var1=$var1 ; var2=$var2 ");

        // or action ...

    }

    protected function handleBotChatStatusUpdate(ChatMemberUpdate $chatMemberUpdate): void
    {

        $update = new DefStudioUpdate($chatMemberUpdate);
        $response = $this->front->handleBotChatStatusUpdate($update);

        if (!$response->continue) {
            return;
        }

    }

    protected function handleCallbackQuery(): void
    {
        $this->extractCallbackQueryData();

        if (config('telegraph.debug_mode', config('telegraph.webhook.debug'))) {
            Log::debug('Telegraph webhook callback', $this->data->toArray());
        }


        // сценарий на кнопки
        $callbackQuery = new DefStudioCallbackQuery($this->callbackQuery);
        $response = $this->front->handleCallbackQuery($callbackQuery);

        if (!$response->continue) {
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

        // сценарий + команды
        $message = new DefStudioMessage($this->message);
        $response = $this->front->handleCommand($message, $command, $parameter);

        if (!$response->continue) {
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
        //сценарий + сообщения
        $message = new DefStudioMessage($this->message);
        $response = $this->front->handleChatMessage($message);
        if (!$response->continue) {
            return;
        }

    }


}
