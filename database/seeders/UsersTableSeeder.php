<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'           => 'Super Admin',
                'email'          => 'superadmin@admin.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_by'     => 1,

            ],
            [
                'sub_admin_id'     => 2,
                'name'           => "Klive's Kitchen",
                'email'          => 'klivekitchen@gmail.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_by'     => 1,
            ],
        ];
        foreach($users as $key=>$user){
            $createdUser =  User::create($user);
        }
    }
}
