<?php

namespace App\Console\Commands;

use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Console\Command;

class SendBotMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:send {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестовое приветствие в основной чат';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        Telegraph::message($this->argument('message'))->dispatch();

        Telegraph::message($this->argument('message'))->send();
    }
}
