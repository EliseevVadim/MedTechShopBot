<?php

namespace App\Core;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use function PHPUnit\Framework\returnArgument;

class TelegramBotHandler extends BaseBot
{
    private $user;

    public function __construct()
    {
        $this->bot = new Api(env("TELEGRAM_BOT_TOKEN"));
    }

    public function createUser($from)
    {
        $telegram_chat_id = $from->id;
        $first_name = $from->first_name ?? null;
        $last_name = $from->last_name ?? null;
        $username = $from->username ?? null;
        $phone = $from->phone ?? null;
        $this->user = User::where("telegram_chat_id", $telegram_chat_id)->first();
        if(is_null($this->user)) {
            $this->user = User::create([
                'full_name' => "$first_name $last_name" ?? null,
                'username' => $username ?? null,
                'telegram_chat_id' => $telegram_chat_id,
                'password' => bcrypt($telegram_chat_id),
                'phone' => $phone ?? null,
                'email' => "$telegram_chat_id@gmail.com"
            ]);
        }
    }

    public function currentUser()
    {
        return $this->user;
    }

    public function bot(): TelegramBotHandler
    {
        return $this;
    }

    public function handler()
    {
        $output = [
            "initial" => "output"
        ];
        $update = $this->bot->getWebhookUpdate();
        include_once base_path('routes/bot.php');
        $item = json_decode($update);
        $message = $item->message ??
            $item->edited_message ??
            $item->callback_query->message ??
            null;
        if (is_null($message))
            return response()->json($output);
        if (isset($update["callback_query"]))
            $this->createUser($item->callback_query->from);
        else
            $this->createUser($message->from);
        $query = $item->message->text ?? $item->callback_query->data ?? '';
        $this->chatId = $message->chat->id;
        $found = false;
        $matches = [];
        $arguments = [];
        foreach ($this->routes as $route) {
            if (is_null($route["path"]) || $route["is_service"])
                continue;
            $data = $route["path"];
            if (preg_match($data . "$/i", $query, $matches)) {
                foreach ($matches as $match)
                    array_push($arguments, $match);
                try {
                    $output = $route["function"]($message, ...$arguments);
                    $found = true;
                }
                catch (\Exception $exception) {
                    Log::error($exception->getMessage() . " " . $exception->getLine());
                }
                break;
            }
        }
        if (!empty($this->next)) {
            foreach ($this->next as $item) {
                try {
                    $output = $item["function"]($message);
                    $found = true;
                }
                catch (\Exception $exception) {
                    Log::error($exception->getMessage() . " " . $exception->getLine());
                }
            }
        }
        if (!$found) {
            $fallbackFound  = false;
            foreach ($this->routes as $route) {
                if (is_null($route["path"]))
                    continue;
                if ($route["path"] == "fallback") {
                    try {
                        $output = $route["function"]($message);
                        $fallbackFound = true;
                    }
                    catch (\Exception $exception) {
                        Log::error($exception->getMessage() . " " . $exception->getLine());
                    }
                }
            }
            if (!$fallbackFound)
                $output = $this->reply("Произошла ошибка обработки запроса. Попробуйте еще.");
        }
        return response()->json($output);
    }
}
