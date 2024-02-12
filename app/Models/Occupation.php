<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Occupation extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'occupations';
    protected $fillable = [
        'name',
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
        static::creating(function(Occupation $model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
