<?php

namespace App\Http\Controllers;

use App\Facades\ShopServiceFacade;
use App\Models\Device;
use Illuminate\Http\Request;

class DevicesController extends Controller
{
    public function confirmDeviceRemoving($message, $route)
    {
        $id = (int)explode(' ', $route)[1];
        $device = Device::find($id);
        ShopServiceFacade::bot()->inlineKeyboard("Вы действительно хотите удалить товар: \"$device->name\"", [
            [
                ["text" => "Да", "callback_data" => "/remove $id"],
                ["text" => "Нет", "callback_data" => "/cancel_item_removing"],
            ],
        ]);
    }

    public function removeDevice($message, $route)
    {
        $id = (int)explode(' ', $route)[1];
        Device::destroy($id);
        ShopServiceFacade::bot()->reply("Товар успешно удален")
            ->next("start");
        ShopServiceFacade::bot()->sendMessageToAllUsers("Товаров были обновлены. Введите /start для отображения изменений.");
    }

    public function cancelRemoving()
    {
        ShopServiceFacade::bot()->reply("Удаление товара отменено.")
            ->next("start");
    }
}
