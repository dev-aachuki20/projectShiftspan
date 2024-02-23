<?php
// app/Rules/EmailHasRole.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserHasRole implements Rule
{
    protected $email;
    protected $role;

    public function __construct($email, $role)
    {
        $this->email = $email;
        $this->role = $role;
    }

    public function passes($attribute, $value)
    {
        return DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('users.email', $this->email)
            ->where('roles.id', $this->role)
            ->exists();
    }

    public function message()
    {
        return trans('messages.required_role');
    }
}
