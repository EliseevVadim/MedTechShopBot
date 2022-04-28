<?php

namespace App\Http\Controllers;

use App\Core\OrdersMailer;
use App\Facades\ShopServiceFacade;
use App\Mail\ConfirmedOrderMail;
use App\Models\Order;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Laravel\Facades\Telegram;

class OrdersController extends Controller
{
    public function addToCart($message, $route)
    {
        $id = (int)explode(' ', $route)[1];
        ShopServiceFacade::bot()->inlineKeyboard("Выберите число единиц выбранного товара:",[
            [
                ["text" => "1", "callback_data" => "/choose 1 $id"],
                ["text" => "2", "callback_data" => "/choose 2 $id"],
            ],
            [
                ["text" => "3", "callback_data" => "/choose 3 $id"],
                ["text" => "4", "callback_data" => "/choose 4 $id"],
            ],
            [
                ["text" => "5", "callback_data" => "/choose 5 $id"],
            ],
            [
                ["text" => "Отмена", "callback_data" => "/cancelOrdering"]
            ]
        ]);
    }

    public function chooseQuantity($message, $route)
    {
        $data = explode(' ', $route);
        $id = $data[2];
        $quantity = $data[1];
        $serviceInfo = Device::where('id', $id)->select('name', 'price', 'discount')->first();;
        $sum = (new Order())->calculateSum($serviceInfo, $quantity);
        ShopServiceFacade::bot()->inlineKeyboard("Действительно ли Вы хотите заказать
<b>устройство:</b> $serviceInfo->name
<b>в количестве:</b> $quantity?
<b>Итоговая цена составит:</b> $sum ₽", [
            [
                ["text" => "Да (добавить в заказы)", "callback_data" => "/confirm $quantity $id"],
                ["text" => "Нет", "callback_data" => "/cancelOrdering"]
            ]
        ]);
    }

    public function cancelOrdering()
    {
        ShopServiceFacade::bot()->reply("Процедура заказа услуги отменена.")
            ->next("start");
    }

    public function confirmOrdering($message, $route)
    {
        try {
            $data = explode(' ', $route);
            $quantity = $data[1];
            $id = (int)$data[2];
            $deviceInfo = Device::where('id', $id)->select('name', 'price', 'discount')->first();
            $order = new Order;
            $order->user_id = ShopServiceFacade::bot()->currentUser()->id;
            $order->device_id = $id;
            $order->quantity = $quantity;
            $order->sum = $order->calculateSum($deviceInfo, $quantity);
            $order->save();
            $orderedService = Device::find($id);
            $orderedService->orders_number += (int)$quantity;
            $orderedService->save();
            (new OrdersMailer())->sendMessage($order, $deviceInfo);
            ShopServiceFacade::bot()->reply("Заказ успешно оформлен. Оплатить его Вы сможете во вкладке \"Заказы\" пользовательского меню.")
                ->next("start");
        }
        catch (\Exception $exception) {
            ShopServiceFacade::bot()->reply($exception->getMessage());
        }
    }

    public function listOrders($message)
    {
        $userId = ShopServiceFacade::bot()->currentUser()->id;
        $orders = Order::join('devices', 'devices.id', '=', 'orders.device_id')
                        ->where('orders.user_id', $userId)
                        ->where('orders.state_id', 1)
                        ->get(['orders.*', 'devices.*']);
        foreach ($orders as $order) {
            ShopServiceFacade::bot()->replyInvoice($order->name, $order->description, [
                ["label" => "Количество единиц товара - $order->quantity", "amount" => $order->sum * 100]
            ], "data");
        }
    }
}
