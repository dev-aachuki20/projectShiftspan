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
        //role_edit,role_show, role_access profile_access profile_edit user_change_password dashboard_widget_access admin_access admin_create admin_edit admin_delete staff_access staff_create staff_edit staff_delete shift_access shift_create  shift_edit shift_delete shift_status_update shift_rating client_access  client_create client_edit client_delete location_access location_create location_edit location_delete occupation_access occupation_create occupation_edit occupation_status_update occupation_delete setting_access setting_edit

        $roles = Role::all();
        $superadminpermissionid= Permission::all();
        $adminpermissionid= Permission::whereIn('name',['role_edit','role_show', 'role_access', 'profile_access', 'profile_edit' ,'user_change_password', 'dashboard_widget_access' ,'admin_access', 'admin_create', 'admin_edit', 'admin_delete' ,'staff_access' ,'staff_create', 'staff_edit', 'staff_delete', 'shift_access', 'shift_create',  'shift_edit' ,'shift_delete','shift_status_update','shift_rating', 'client_access' , 'client_create', 'client_edit' ,'client_delete', 'location_access', 'location_create', 'location_edit' ,'location_delete', 'occupation_access' ,'occupation_create', 'occupation_edit', 'occupation_status_update', 'occupation_delete' ,'setting_access', 'setting_editt'])->pluck('id')->toArray();

        $staffpermissionid= Permission::whereIn('name',['profile_access'])->pluck('id')->toArray();

        foreach ($roles as $role) {
            switch ($role->id) {
                case 1:
                    $role->permissions()->sync($superadminpermissionid);
                    //$role->givePermissionTo($superadminpermissionid);
                    break;
                case 2:
                    $role->permissions()->sync($adminpermissionid);
                    //$role->givePermissionTo($adminpermissionid);
                    break;
                case 3:
                    $role->permissions()->sync($staffpermissionid);
                    //$role->givePermissionTo($staffpermissionid);
                    break;
                default:
                    break;
            }
        }
    }
}
