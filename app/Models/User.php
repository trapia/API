<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // الأعمدة اللي هيتم حفظها
    protected $fillable = [
      'name',
      'email',
      'phone',
      'password',
      'bag_id',
      'image',
  ];
  
    // الأعمدة اللي هتكون مخفية
    protected $hidden = [
        'password',
    ];

    // دالة للحصول على JWT identifier
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function bag()
    {
        return $this->hasOne(Bag::class);
    }
    public function notifications()
{
    return $this->hasMany(Notification::class);
}

    // دالة للحصول على JWT claims (اختياري، ممكن تضيف بيانات تانية هنا)
    public function getJWTCustomClaims()
    {
        return [];
    }
}
