<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $guard = 'web';

    public $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'dob',
        'previous_name',
        'national_insurance_number',
        'address',
        'education',
        'prev_emp_1',
        'prev_emp_2',
        'reference_1',
        'reference_2',
        'date_sign',
        'is_rehabilite',
        'is_enquire',
        'is_health_issue',
        'is_statement',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
