<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'quantity',
        'sum',
        'state_id'
    ];

    public function calculateSum($serviceInfo, $quantity)
    {
        return !is_null($serviceInfo->discount) ? ($serviceInfo->price - $serviceInfo->price * $serviceInfo->discount / 100) * $quantity
            : $serviceInfo->price * $quantity;
    }
}
