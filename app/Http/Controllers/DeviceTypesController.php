<?php

namespace App\Http\Controllers;

use App\Facades\ShopServiceFacade;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;

class DeviceTypesController extends Controller
{
    private const RecordsPerPage = 5;
    public function loadAllTypes()
    {
        $keyboard = self::createDeviceTypesKeyboard(false);
        ShopServiceFacade::bot()->inlineKeyboard("Выберите интересующую Вас категорию:", $keyboard);
    }

    public function getDevicesList($message, $route)
    {
        $data = explode(' ', $route);
        $typeId = (int)$data[1];
        $page = (int)$data[2];
        $nextPage = $page + 1;
        $prevPage = $page - 1;
        $name = DeviceType::where('id', $typeId)->select('type_name')->first()->type_name;
        $devicesCount = Device::where('type_id', $typeId)->count();
        if ($devicesCount === 0)
        {
            ShopServiceFacade::bot()->reply("Товаров из категории \"$name\" не найдено");
            return;
        }
        $devices = Device::where('type_id', $typeId)
            ->take(self::RecordsPerPage)
            ->skip(self::RecordsPerPage * $page)
            ->get();
        ShopServiceFacade::bot()->reply("Товары из категории \"$name\":");
        foreach ($devices as $device) {
            $price = $device->discount != null ? $device->price - ($device->price * $device->discount / 100) : $device->price;
            $path = InputFile::create(storage_path('app/public/images/'.$device->image_path));
            ShopServiceFacade::bot()->sendDevice($device, $path, [
                [
                    ["text" => "Добавить в коризину ($price ₽)", "callback_data" => "/addToCart $device->id"]
                ],
                ShopServiceFacade::bot()->currentUser()->role_id == 2 ? [
                    ["text" => "Удалить товар", "callback_data" => "/removeItem $device->id"]
                ] : []
            ]);
        }
        if ($devicesCount > self::RecordsPerPage) {
            $pagesCount = ceil($devicesCount / self::RecordsPerPage);
            $navKeyboard = [
                $prevPage >= 0 ?
                [
                    ["text" => "Предыдущей", "callback_data" => "/type $typeId $prevPage"],
                ] : [],
                $nextPage < $pagesCount  ?
                [
                    ["text" => "Следующей", "callback_data" => "/type $typeId $nextPage"]
                ] : []
            ];
            $temp = [];
            for ($i = 0; $i < $pagesCount; $i++) {
                $actualNumber = $i + 1;
                array_push($temp, ["text" => $actualNumber, "callback_data" => "/type $typeId $i"]);
            }
            array_push($navKeyboard, $temp);
            ShopServiceFacade::bot()->inlineKeyboard("Перейти к странице:", $navKeyboard);
        }
    }

    public function prepareDevicesCategoryForRemoving($message)
    {
        $keyboard = self::createDeviceTypesKeyboard(true);
        ShopServiceFacade::bot()->replyEditedMessage($message->message_id, "Выберите удаляемую категорию товаров", $keyboard);
    }

    public function createDeviceTypesKeyboard($forDeleting)
    {
        $types = DeviceType::query()->select('id', 'type_name')->get();
        $keyboard = [];
        $index = 0;
        $temp = [];
        foreach ($types as $type) {
            $count = Device::where('type_id', $type->id)->count();
            $index++;
            if ($forDeleting)
                array_push($temp, ["text" => "$type->type_name ($count)", "callback_data" => "/removeCategory $type->id"]);
            else
                array_push($temp, ["text" => "$type->type_name ($count)", "callback_data" => "/type $type->id 0"]);
            if ($index % 2 == 0 || $index == count($types)) {
                array_push($keyboard, $temp);
                $temp = [];
            }
        }
        return $keyboard;
    }

    public function removeCategory($message, $route)
    {
        try {
            $id = explode(' ', $route)[1];
            $name = DeviceType::where('id', $id)->select('type_name')->first();
            ShopServiceFacade::bot()->replyEditedMessage($message->message_id, "Вы действительно хотите удалить категорию товаров и все товары в ней: $name->type_name ?", [
                [
                    ["text" => "Да", "callback_data" => "/acceptRemoving $id"],
                    ["text" => "Нет", "callback_data" => "/cancelRemoving"]
                ]
            ]);
        }
        catch (\Exception $exception) {
            ShopServiceFacade::bot()->reply($exception->getMessage());
        }
    }

    public function cancelRemoving()
    {
        ShopServiceFacade::bot()->reply("Удаление категории товаров отменено.")
            ->next("start");
    }

    public function acceptRemoving($message, $route)
    {
        $id = explode(' ', $route)[1];
        DeviceType::destroy($id);
        ShopServiceFacade::bot()->reply("Категория товаров успешно удалена")
            ->next("start");
        ShopServiceFacade::bot()->sendMessageToAllUsers("Категории товаров были обновлены. Введите /start для отображения изменений.");
    }
}
