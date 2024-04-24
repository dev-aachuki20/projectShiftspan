<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'locations';
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_active',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Location $model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user()->id;
        });
    }

    public function clientDetails()
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function subAdmins()
    {
        return $this->belongsToMany(User::class);
    }
}
