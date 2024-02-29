<?php
// app/Rules/EmailHasRole.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserHasRole implements Rule
{
    protected $email;
    protected $role;
    protected $type;
    protected $attribute;

    public function __construct($role, $email='', $type='email')
    {
        $this->email = $email;
        $this->role = $role;
        $this->type = $type;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        if($this->type == 'uuid'){
            $col = 'users.uuid';
            $val = $value;
        } else {
            $col = 'users.email';
            $val = $this->email;
        }
        return DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where($col, $val)
                ->where('roles.id', $this->role)
                ->exists();
    }

    public function message()
    {
        if($this->type == 'uuid'){
            if($this->attribute == 'sub_admin_id'){
                return 'The selected client name is invalid.';
            } else {
                return 'The selected staff is invalid.';
            }
        }
        return trans('messages.required_role');
    }
}
