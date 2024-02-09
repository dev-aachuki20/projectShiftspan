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
                'username'       => 'superadmin',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_by'     => 1,
            
            ],
            [  
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'username'       => 'admin',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_by'     => 1, 
            ],
            [   
                'name'           => 'Rohan',
                'email'          => 'rohan@gmail.com',
                'username'       => 'rohan12345',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_by'     => 1,
            ]
        ];
        foreach($users as $key=>$user){
            $createdUser =  User::create($user);
        }
    }
}
