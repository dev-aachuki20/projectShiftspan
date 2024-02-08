<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'clients';
    protected $fillable = [
        'name',
        'address',
        'shop_description',
        'parking_description',
        'sub_admin_id',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_active',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Client $model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function clientImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'client');
    }

    public function getClientImageUrlAttribute()
    {
        if ($this->clientImage) {
            return $this->clientImage->file_url;
        }
        return "";
    }

}
