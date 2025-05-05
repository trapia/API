<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // الحقول اللي هتتم تعبئتها
    protected $fillable = [
        'user_id',    // معرف المستخدم
        'title',      // عنوان الإشعار
        'message',    // نص الإشعار
        'status',     // حالة الإشعار (قرأت أم لا)
    ];

    // العلاقة بين الإشعار والمستخدم (إشعار مخصص لكل مستخدم)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
