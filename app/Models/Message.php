<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    public $table = 'messages';

    protected $fillable = [
        'id',
        'uuid',
        'user_id',
        'group_id',
        'content',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Message $model) {
            $model->uuid = Str::uuid();
            $model->user_id = auth()->user()->id;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id','id');
    }

    public function usersSeen()
    {
        return $this->belongsToMany(User::class, 'message_seen', 'message_id','user_id')
        ->withPivot('group_id', 'read_at');
    }

}
