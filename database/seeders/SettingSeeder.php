<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key'    => 'site_title',
                'value'  => 'Shift Span App',
                'type'   => 'text',
                'display_name'  => 'Title Name',
                'group'  => 'web',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'site_logo',
                'value'  => null,
                'type'   => 'image',
                'details' => null,
                'display_name'=>'Site Logo',
                'group'  => 'web',
                'status' => 1,
                'position' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'favicon',
                'value'  => null,
                'type'   => 'image',
                'details' => null,
                'display_name'=>'Favicon Icon',
                'group'  => 'web',
                'status' => 0,
                'position' => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            
            [
                'key'    => 'help_pdf',
                'value'  => null,
                'type'   => 'file',
                'details' => null,
                'display_name'=>'Help Pdf',
                'group'  => 'web',
                'status' => 1,
                'position' => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'support_email',
                'value'  => 'shiftspan@gmail.com',
                'type'   => 'text',
                'display_name'  => 'Contact Email',
                'group'  => 'support',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'support_phone',
                'value'  => '',
                'type'   => 'text',
                'display_name'  => 'Contact Mobile',
                'group'  => 'support',
                'details' => null,
                'status' => 1,
                'position' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ], 
            [
                'key'    => 'privacy_policy',
                'value'  => '',
                'type'   => 'file',
                'display_name'  => 'Privacy Policy',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ], 
            [
                'key'    => 'gdpr_policy',
                'value'  => '',
                'type'   => 'file',
                'display_name'  => 'GDPR Policy',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ], 
            [
                'key'    => 'dbs_copy',
                'value'  => '',
                'type'   => 'file',
                'display_name'  => 'DBS Copy',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ], 
            [
                'key'    => 'other',
                'value'  => '',
                'type'   => 'file',
                'display_name'  => 'Other',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],            
        ];
        Setting::insert($settings);
    }
}
