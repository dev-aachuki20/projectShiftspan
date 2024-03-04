<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $updateDate = $createDate = date('Y-m-d H:i:s');
        $permissions = [
            // [
            //     'name'      => 'permission_create',
            //
            //     'route_name'=>'permissions',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            // [
            //     'name'      => 'permission_edit',
            //
            //     'route_name'=>'permissions',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            // [
            //     'name'      => 'permission_show',
            //
            //     'route_name'=>'permissions',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            // [
            //     'name'      => 'permission_delete',
            //
            //     'route_name'=>'permissions',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            // [
            //     'name'      => 'permission_access',
            //
            //     'route_name'=>'permissions',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            [
                'name'      => 'role_access',
                'title'      => 'Menu Access',
                'route_name'=>'roles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'role_edit',
                'title'      => 'Edit',
                'route_name'=>'roles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'role_show',
                'title'      => 'View',
                'route_name'=>'roles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'profile_access',
                'title'      => 'View',
                'route_name'=>'profiles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'profile_edit',
                'title'      => 'Edit',
                'route_name'=>'profiles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'user_change_password',
                'title'      => 'Change Password',
                'route_name'=>'profiles',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'dashboard_widget_access',
                'title'      => 'Dashboard Widget Access',
                'route_name'=>'dashboard',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_access',
                'title'      => 'Menu Access',
                'route_name'=>'admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_create',
                'title'      => 'Add',
                'route_name'=>'admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_edit',
                'title'      => 'Edit',
                'route_name'=>'admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_delete',
                'title'      => 'Delete',
                'route_name'=>'admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'staff_access',
                'title'      => 'Menu Access',
                'route_name'=>'staff',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'staff_create',
                'title'      => 'Add',
                'route_name'=>'staff',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'staff_edit',
                'title'      => 'Edit',
                'route_name'=>'staff',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'staff_view',
                'title'      => 'View',
                'route_name'=>'staffs',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'staff_delete',
                'title'      => 'Delete',
                'route_name'=>'staff',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_access',
                'title'      => 'Menu Access',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_create',
                'title'      => 'Add',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_edit',
                'title'      => 'Edit',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_delete',
                'title'      => 'Delete',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_status_update',
                'title'      => 'Change Status',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'shift_rating',
                'title'      => 'Add Rating',
                'route_name'=>'shift',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_detail_access',
                'title'      => 'Menu Access',
                'route_name'=>'client',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_detail_create',
                'title'      => 'Add',
                'route_name'=>'client',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_detail_edit',
                'title'      => 'Edit',
                'route_name'=>'client',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_detail_view',
                'title'      => 'View',
                'route_name'=>'client',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'sub_admin_detail_delete',
                'title'      => 'Delete',
                'route_name'=>'client',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'location_access',
                'title'      => 'Menu Access',
                'route_name'=>'location',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'location_create',
                'title'      => 'Add',
                'route_name'=>'location',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'location_edit',
                'title'      => 'Edit',
                'route_name'=>'location',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'location_delete',
                'title'      => 'Delete',
                'route_name'=>'location',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'occupation_access',
                'title'      => 'Menu Access',
                'route_name'=>'occupation',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'occupation_create',
                'title'      => 'Add',
                'route_name'=>'occupation',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'occupation_edit',
                'title'      => 'Edit',
                'route_name'=>'occupation',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'occupation_status_update',
                'title'      => 'Change Status',
                'route_name'=>'occupation',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'occupation_delete',
                'title'      => 'Delete',
                'route_name'=>'occupation',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'setting_access',
                'title'      => 'Setting Menu Access',
                'route_name'=>'settings',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'setting_edit',
                'title'      => 'Edit',
                'route_name'=>'settings',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'      => 'notification_create',
                'title'      => 'Add',
                'route_name'=> 'staffs.notificationStore',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'       => 'message_access',
                'title'      => 'Message Access',
                'route_name' => 'messages',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'       => 'message_create',
                'title'      => 'Add',
                'route_name' => 'messages',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'       => 'message_edit',
                'title'      => 'Edit',
                'route_name' => 'messages',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'       => 'message_view',
                'title'      => 'View',
                'route_name' => 'messages',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'name'       => 'message_delete',
                'title'      => 'Delete',
                'route_name' => 'messages',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
        ];
        Permission::insert($permissions);
    }
}
