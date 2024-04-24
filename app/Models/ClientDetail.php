<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ClientDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guard = 'web';
    public $table = 'client_details';
    protected $fillable = [
        'uuid',
        'sub_admin_id',
        'location_id',
        'name',
        'address',
        'shop_description',
        'travel_info',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(ClientDetail $model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user()->id;
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function buildingImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'client-building-image');
    }

    public function getBuildingImageUrlAttribute()
    {
        if ($this->buildingImage) {
            return $this->buildingImage->file_url;
        }
        return "";
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'sub_admin_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
