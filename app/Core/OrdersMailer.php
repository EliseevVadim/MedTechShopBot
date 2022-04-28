<?php

namespace App\Core;

use App\Facades\ShopServiceFacade;
use App\Mail\ConfirmedOrderMail;
use Illuminate\Support\Facades\Mail;

class OrdersMailer
{
    public function sendMessage($order, $serviceInfo)
    {
        $details = [
            'title' => "Оповещение о заказе",
            'date' => date('d.m.Y  H:i:s'),
            'service_name' => $serviceInfo->name,
            'customers_name' => ShopServiceFacade::bot()->currentUser()->full_name,
            'quantity' => $order->quantity,
            'sum' => $order->sum,
            'price' => $serviceInfo->price,
            'discount' => !is_null($serviceInfo->discount) ? $serviceInfo->discount."%" : "отсутствует",
            'user_mail' => ShopServiceFacade::bot()->currentUser()->email
        ];
        Mail::to(env("MAIL_RECEIVER"))->send(new ConfirmedOrderMail($details));
    }
}
