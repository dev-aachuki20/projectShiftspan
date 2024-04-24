<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Chat extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';

    public $table = 'chats';

    protected $fillable = [
        'id',
        'message_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot ()
    {
        parent::boot();
    }

    public function message()
    {
        return $this->belongsTo(Message::class,'message_id','id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'users_chats');
    }

}
