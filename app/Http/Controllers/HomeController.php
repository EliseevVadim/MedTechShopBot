<?php

namespace App\Http\Controllers;

use App\Facades\ShopServiceFacade;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function openTinkerPage()
    {
        if (is_null(session('user_id')))
            abort(401);
        return view('tinkerPage');
    }
}
