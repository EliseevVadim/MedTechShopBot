<?php

namespace App\Core;

abstract class BotCore
{
    protected $routes = [];
    protected $next = [];

    public function next($name): BotCore
    {
        foreach ($this->routes as $route) {
            if (isset($route["name"])) {
                if ($route["name"] == $name) {
                    array_push($this->next, [
                       "name" => $name,
                       "function" => $route["function"]
                    ]);
                }
            }
        }
        return $this;
    }

    public function addRoute($path, $function, $name = null) : TelegramBotHandler
    {
        array_push($this->routes, [
            "path" => $path,
            "is_service" => false,
            "function" => $function,
            "name" => $name
        ]);
        return $this;
    }

    public function addRouteFallback($function) : TelegramBotHandler
    {
        array_push($this->routes, [
            "path" => "fallback",
            "is_service" => true,
            "function" => $function
        ]);
        return $this;
    }
}
