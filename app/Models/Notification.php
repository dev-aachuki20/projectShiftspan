<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "notifications";

    protected $primarykey = "id";

    protected $fillable = [
        'id',
        'type',
        'notifiable',
        'data',
        'subject',
        'message',
        'section',
        'notification_type',
        'created_by',
        'read_at',        
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
        'id' => 'string'
    ];


    protected $appends = ['user'];

    /* protected static function boot ()
    {
        parent::boot();
        static::creating(function(Notification $model) {
            $model->id = Str::uuid();
            $model->type = 'user';
            $model->created_by = auth()->user()->id;
        });
    } */

    public function getUserAttribute()
    {
        $user = User::find($this->notifiable_id);
        return $user ? $user->company_id : null;
    }


}
