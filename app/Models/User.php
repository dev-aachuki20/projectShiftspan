<?php

namespace App\Models;

use App\Notifications\OtpSendNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = 'web';

    public $table = 'users';

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'username',
        'email',
        'phone',
        'password',
        'sub_admin_id',
        'company_number',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(User $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.super_admin'))->exists();
    }

    public function getIsSubAdminAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.sub_admin'))->exists();
    }

    public function getIsStaffAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.staff'))->exists();
    }

    public function occupations(){
        return $this->belongsToMany(Occupation::class);
    }

    public function company(){
        return $this->belongsTo(User::class,'company_id','id');
    }

    public function sendPasswordResetOtpNotification($user,$token, $subject , $expiretime)
    {
        $this->notify(new OtpSendNotification($user,$token, $subject , $expiretime));
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    // profile image

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_profile');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    // Training Document

    public function trainingDocument()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_training_doc');
    }

    public function getTrainingDocumentUrlAttribute()
    {
        if ($this->trainingDocument) {
            return $this->trainingDocument->file_url;
        }
        return "";
    }

    // DBS Certificate
    public function dbsCertificate()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_dbs_certificate');
    }

    public function getDbsCertificateUrlAttribute()
    {
        if ($this->dbsCertificate) {
            return $this->dbsCertificate->file_url;
        }
        return "";
    }

    // CV
    public function cv()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_cv');
    }

    public function getCVUrlAttribute()
    {
        if ($this->cv) {
            return $this->cv->file_url;
        }
        return "";
    }

    // Staff Budge
    public function staffBudge()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_staff_budge');
    }

    public function getStaffBudgeUrlAttribute()
    {
        if ($this->staffBudge) {
            return $this->staffBudge->file_url;
        }
        return "";
    }

    // DBS Check
    public function dbsCheck()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_dbs_check');
    }

    public function getDbsCheckUrlAttribute()
    {
        if ($this->dbsCheck) {
            return $this->dbsCheck->file_url;
        }
        return "";
    }

    // Training Check
    public function trainingCheck()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_training_check');
    }

    public function getTrainingCheckUrlAttribute()
    {
        if ($this->trainingCheck) {
            return $this->trainingCheck->file_url;
        }
        return "";
    }

    public function clockInOutRecords()
    {
        return $this->hasMany(ClockInOut::class, 'user_id');
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'staff_shift', 'user_id', 'shift_id');
    }
    public function locations(){
        return $this->belongsToMany(Location::class);
    }

    public function staffs(){
        return $this->hasMany(User::class, 'company_id', 'id');
    }
}
