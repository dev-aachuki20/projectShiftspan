<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $guard = 'web';

    public $table = 'companies';

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

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
