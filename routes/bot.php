<?php

use App\Facades\ShopServiceFacade;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\DeviceTypesController;
use App\Models\Order;
use App\Models\DeviceType;

ShopServiceFacade::bot()
    ->addRoute("/start", function ($message) {
        $categories = DeviceType::all()->count();
        $id = ShopServiceFacade::bot()->currentUser()->id;
        $ordersNum = Order::where('user_id', $id)->count();
        ShopServiceFacade::bot()->replyKeyboard(
            "<b>Доброго времени суток!</b>\nВас приветствует бот-помощник автосервиса N. Для дальнейшей работы выберите один из предложенных ниже разделов:",
        [
            [
                ["text" => "Категории товаров ($categories)"]
            ],
            [
                ["text" => "Справка"]
            ],
            [
                ["text" => "Заказы ($ordersNum)"]
            ],
            ShopServiceFacade::bot()->currentUser()->role_id == 2 ? [
                ["text" => "Только для модераторов"]
            ] : []
        ]);
    }, "start");

ShopServiceFacade::bot()->addRoute("/Категории товаров ([()0-9]+)", [DeviceTypesController::class, "loadAllTypes"]);

ShopServiceFacade::bot()->addRoute("/Только для модераторов", function ($message) {
    ShopServiceFacade::bot()->inlineKeyboard("Выберите действие:", [
        [
            ["text" => "Добавить товар", "url" => "http://127.0.0.1:8000/openDeviceAdding"]
        ],
        [
            ["text" => "Добавить категорию товаров", "url" => "http://127.0.0.1:8000/addDeviceType"]
        ],
        [
            ["text" => "Удалить категорию товаров", "callback_data" => "/startCategoryRemoving"]
        ],
        [
            ["text" => "Просмотр пользовательских сообщений", "url" => "http://127.0.0.1:8000/checkMessages"]
        ]
    ]);
});

ShopServiceFacade::bot()->addRoute("/type [()0-9]+ [()0-9]+", [DeviceTypesController::class, "getDevicesList"]);

ShopServiceFacade::bot()->addRoute("/addToCart [()0-9]+", [OrdersController::class, "addToCart"]);

ShopServiceFacade::bot()->addRoute("/removeItem [()0-9]+", [DevicesController::class, "confirmDeviceRemoving"]);

ShopServiceFacade::bot()->addRoute("/remove [()0-9]+", [DevicesController::class, "removeDevice"]);

ShopServiceFacade::bot()->addRoute("/cancel_item_removing", [DevicesController::class, "cancelRemoving"]);

ShopServiceFacade::bot()->addRoute("/choose [()0-9]+ [()0-9]+", [OrdersController::class, "chooseQuantity"]);

ShopServiceFacade::bot()->addRoute("/cancelOrdering", [OrdersController::class, "cancelOrdering"]);

ShopServiceFacade::bot()->addRoute("/confirm [()0-9]+ [()0-9]+", [OrdersController::class, "confirmOrdering"]);

ShopServiceFacade::bot()->addRoute("/Заказы ([()0-9]+)", [OrdersController::class, "listOrders"]);

ShopServiceFacade::bot()->addRoute("/Справка", function ($message) {
    $response = "<b>Полезная информация о боте:\n</b>
Данный бот предназначен для удобного взаимодействия с каталогом медтехники N.\n
Рядовые пользователи имеют возможность просматривать категории товаров и товары, принадлежащие им в удобной форме с возможностью заказа.\n
Для того, чтобы заказать тот или иной товар, необходимо нажать на соответсвтующую кнопку. После этого необходимо выбрать число единиц товара в соответствующем сообщении. Максимально можно заказать до 5 единиц товара за один заказ.\n
После выбора числа единиц товара и подтверждения выбора заказ оформляется и становится готовым к оплате.\n
Оплатить заказ можно перейдя во вкладку \"Заказы\" пользовательского меню и проведя стандартную процедуру оплаты.
\n\n\n
С уважением, Администрация!";
    ShopServiceFacade::bot()->reply($response);
});

ShopServiceFacade::bot()->addRoute("/startCategoryRemoving", [DeviceTypesController::class, "prepareDevicesCategoryForRemoving"]);

ShopServiceFacade::bot()->addRoute("/removeCategory [()0-9]+", [DeviceTypesController::class, "removeCategory"]);

ShopServiceFacade::bot()->addRoute("/cancelRemoving", [DeviceTypesController::class, "cancelRemoving"]);

ShopServiceFacade::bot()->addRoute("/acceptRemoving [()0-9]+", [DeviceTypesController::class, "acceptRemoving"]);
