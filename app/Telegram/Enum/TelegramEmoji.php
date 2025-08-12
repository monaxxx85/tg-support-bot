<?php
namespace App\Telegram\Enum;
enum TelegramEmoji: string
{
    //5417915203100613993 - облачко
    //5377316857231450742 - ?
    //5237699328843200968 - галочка в зеленом квадрате
    //5357121491508928442 - два глаза
    //5386395194029515402 - пиратский флаг
    //5377498341074542641 - !!

    // Базовые иконки
    case DEFAULT_ICON = "5417915203100613993";
    case QUESTION_ICON = "5377316857231450742";
    case ANSWERED_ICON = "5237699328843200968";
    case CLOCK_ICON = "5357121491508928442";
    case BANNED_ICON = "5377498341074542641";

    // Метод для удобного использования
    public function value(): string
    {
        return $this->value;
    }
}
