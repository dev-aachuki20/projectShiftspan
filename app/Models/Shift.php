<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Shift extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = 'web';
    public $table = 'shifts';
    protected $fillable = [
        'sub_admin_id',
        'client_detail_id',
        'location_id',
        'occupation_id',
        'sub_admin_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'picked_at',
        'cancel_at',
        'rating',
        'quantity',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'picked_at',
        'cancel_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(Shift $model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user()->id;
        });
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'sub_admin_id', 'id');
    }

    public function clientDetail()
    {
        return $this->belongsTo(ClientDetail::class, 'client_detail_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'staff_shift', 'shift_id', 'staff_id');
    }

    public function clockInOuts()
    {
        return $this->hasMany(ClockInOut::class);
    }

    public function clockIn(){
        return $this->hasMany(ClockInOut::class)->whereNotNull('clockin_date');
    }
    
    public function clockOut(){
        return $this->hasMany(ClockInOut::class)->whereNotNull('clockout_date');
    }

    public function authorize(){
        return $this->hasOne(AuthorizedShift::class);
    }


}
