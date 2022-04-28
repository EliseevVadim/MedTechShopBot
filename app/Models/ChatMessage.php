<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'content',
        'got_reply'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'chat_id');
    }
}
