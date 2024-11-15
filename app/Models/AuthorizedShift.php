<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class AuthorizedShift extends Model
{
    use HasFactory,HasApiTokens,SoftDeletes;
    protected $table = 'authorized_shifts';

    protected $fillable = [
        'user_id',
        'shift_id',
        'manager_name',
        'authorize_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function authorizedSignature()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'authorize-signature');
    }

    public function getAuthorizedSignatureUrlAttribute()
    {
        if ($this->authorizedSignature && file_exists(public_path($this->authorizedSignature->file_url)) ) {
            return $this->authorizedSignature->file_url;
        }
        return "";
    }
}
