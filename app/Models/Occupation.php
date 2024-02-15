<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Occupation extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'occupations';
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Occupation $model) {
            $model->uuid = Str::uuid();
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

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
