<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class ClockInOut extends Model
{
    use HasFactory,HasApiTokens,SoftDeletes;
    protected $table = 'clock_in_out';

    protected $fillable = [
        'user_id',
        'shift_id',
        'clockin_date',
        'clockout_date',
        'clockin_latitude',
        'clockin_longitude',
        'clockin_address',
        'clockout_latitude',
        'clockout_longitude',
        'clockout_address',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
