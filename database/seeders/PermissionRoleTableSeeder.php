<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //role_edit,role_show, role_access profile_access profile_edit user_change_password dashboard_widget_access sub_admin_access sub_admin_create sub_admin_edit sub_admin_delete staff_access staff_create staff_edit staff_delete shift_access shift_create  shift_edit shift_delete shift_status_update shift_rating sub_admin_detail_access  sub_admin_detail_create sub_admin_detail_edit sub_admin_detail_delete location_access location_create location_edit location_delete occupation_access occupation_create occupation_edit occupation_status_update occupation_delete setting_access setting_edit

        $roles = Role::all();
        $super_admin_permission_ids = Permission::all();
        $sub_admin_permission_ids = Permission::whereIn('name',['role_edit','role_show', 'role_access', 'profile_access', 'profile_edit' ,'user_change_password', 'dashboard_widget_access' ,'staff_access' ,'staff_create', 'staff_edit', 'staff_delete', 'shift_access', 'shift_create',  'shift_edit' ,'shift_delete','shift_status_update','shift_rating', 'sub_admin_detail_access' , 'sub_admin_detail_create', 'sub_admin_detail_edit' ,'sub_admin_detail_delete', 'location_access', 'location_create', 'location_edit' ,'location_delete', 'occupation_access' ,'occupation_create', 'occupation_edit', 'occupation_status_update', 'occupation_delete' ,'setting_access', 'setting_edit'])->pluck('id')->toArray();

        $staff_permission_ids = Permission::whereIn('name',['profile_access'])->pluck('id')->toArray();

        foreach ($roles as $role) {
            switch ($role->id) {
                case 1:
                    $role->permissions()->sync($super_admin_permission_ids);
                    //$role->givePermissionTo($super_admin_permission_ids);
                    break;
                case 2:
                    $role->permissions()->sync($sub_admin_permission_ids);
                    //$role->givePermissionTo($sub_admin_permission_ids);
                    break;
                case 3:
                    $role->permissions()->sync($sub_admin_permission_ids);
                    //$role->givePermissionTo($sub_admin_permission_ids);
                    break;
                default:
                    break;
            }
        }
    }
}
