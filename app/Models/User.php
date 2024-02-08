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
        'name',
        'email',
        'username',
        'phone',
        'password',
        'profile_id',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'is_active',
        'email_verified_at',
        'otp',
        'subject',
        'expiretime',
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

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    // public function sendPasswordResetOtpNotification($request, $user)
    // {
    //     $this->notify(new OtpSendNotification($user));
    // }

    public function routeNotificationForMail()
    {
        return $this->email;
    }



    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

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

    public function TrainingDocumentUrlAttribute()
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

    public function DbsCertificateUrlAttribute()
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

    public function CVUrlAttribute()
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

    public function StaffBudgeUrlAttribute()
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

    public function DbsCheckUrlAttribute()
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

    public function TrainingCheckUrlAttribute()
    {
        if ($this->trainingCheck) {
            return $this->trainingCheck->file_url;
        }
        return "";
    }

    // Other Docs
    public function otherDoc()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_other_doc');
    }

    public function OtherDocUrlAttribute()
    {
        if ($this->otherDoc) {
            return $this->otherDoc->file_url;
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

}
