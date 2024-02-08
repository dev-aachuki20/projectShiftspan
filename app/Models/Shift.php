<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Shift extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'shifts';
    protected $fillable = [
        'client_id',
        'user_id',
        'location_id',
        'occupation_id',
        'start_date',
        'end_date',
        'approval_date',
        'cancel_date',
        'rating',
        'quantity',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Shift $model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'staff_shift', 'shift_id', 'user_id');
    }

}
