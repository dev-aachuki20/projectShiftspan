<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Home extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'homes';
    protected $fillable = [
        'name',
        'address',
        'shop_descp',
        'parking_descp',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_active',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Company $model) {
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

    public function homeImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'home');
    }

    public function getHomeImageUrlAttribute()
    {
        if ($this->homeImage) {
            return $this->homeImage->file_url;
        }
        return "";
    }

}
