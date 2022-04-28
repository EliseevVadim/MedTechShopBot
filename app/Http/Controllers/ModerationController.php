<?php

namespace App\Http\Controllers;

use App\Facades\ShopServiceFacade;
use App\Models\ChatMessage;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModerationController extends Controller
{
    public function openDeviceAdding()
    {
        $types = DeviceType::query()->select('id', 'type_name')->get();
        return view('addDevice', compact('types'));
    }

    public function openDeviceTypeAdding()
    {
        return view('addDeviceType');
    }

    public function addDevice(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer',
            'discount' => 'nullable|integer|between:0,100',
            'image' => 'required|mimes:png,jpg,jpeg,gif'
        ]);
        $file = $request->file('image');
        $filename = uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = Storage::disk('uploads')->putFileAs('/', $file, $filename);
        $device = new Device;
        $device->fill($request->all());
        $device->image_path = $path;
        $device->save();
        ShopServiceFacade::bot()->sendMessageToAllUsers("Товары были обновлены. Введите /start для отображения изменений.");
        return redirect("openDeviceAdding");
    }

    public function addDeviceType(Request $request)
    {
        $request = $request->validate([
            "type_name" => "required|string"
        ]);
        $deviceType = new DeviceType;
        $deviceType->fill($request);
        $deviceType->save();
        ShopServiceFacade::bot()->sendMessageToAllUsers("Категории товаров были обновлены. Введите /start для отображения изменений.");
        return redirect()->route('typeAdding');
    }

    public function openMessagesPage()
    {
        $messages = ChatMessage::where('got_reply', '=', false)
            ->join('users', 'users.telegram_chat_id', '=', 'chat_messages.chat_id')
            ->select('chat_messages.id', 'chat_messages.content', 'users.full_name')
            ->get();
        return view('checkMessages', compact('messages'));
    }

    public function deleteMessage($id)
    {
        try {
            ChatMessage::destroy($id);
        }
        catch (\Exception $exception) {
            die("Произошла ошибка удаления.");
        }
    }

    public function replyMessage(Request $request)
    {
        $message = ChatMessage::find($request->message_id);
        ShopServiceFacade::bot()->sendMessageToUserById($request->answer, $message->chat_id);
        $message->got_reply = true;
        $message->save();
    }
}
