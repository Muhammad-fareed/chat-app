<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable=[
        "receiver_id",
        "sender_id",
        "body",
        "receiver_deleted_at",
        "sender_deleted_at",
        "conversation_id",
        "read_at"
    ];

    protected $dates=["read_at","sender_deleted_at","receiver_deleted_at"];

    public function conservation(){
        return $this->belongsTo(Conversation::class);
    }

    public function isRead():bool{
        return $this->read_at!= null;
    }
}
