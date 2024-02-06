<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;


class Location extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'locations';
    protected $fillable = [
        'name',
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
}
