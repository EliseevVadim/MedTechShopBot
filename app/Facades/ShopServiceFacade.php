<?php

namespace App\Facades;

use App\Core\TelegramBotHandler;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TelegramBotHandler bot()
 * @see Logger
 */
class ShopServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shop.service';
    }
}
