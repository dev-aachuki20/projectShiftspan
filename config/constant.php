<?php

return [
    'default' => [
        'logo' => 'default/logo.png',
        'favicon' => 'default/favicon.png',
        'no_image' => 'default/no-image.jpg',
        'staff-image' => 'default/staff-img.png',
        'building-image' => 'default/building-image.png',
        'help_pdf' => 'default/help_pdf.pdf',
        'user_icon' => 'default/user-icon.svg',
        'datatable_loader' => 'default/datatable_loader.gif',
    ],
    'profile_max_size' => 2048,
    'profile_max_size_in_mb' => '2MB',

    'roles' =>[
        'super_admin' => 1,
        'sub_admin' => 2,
        'staff' => 3,
    ],

    'user_status' => [
        1 => 'Active',
        0 => 'Deactive'
    ],

    'staff_info' => [
        1 => 'Yes',
        0 => 'No'
    ],

    'shop_description_length' => 200, 
    'travel_info_length' => 200, 
    'company_number_prefix' => 'SS', 
    'company_number_start' => '000001', 

    'staff_file_fields' => [
        'user_dbs_certificate',
        'user_cv',
        'user_training_doc',
        'user_staff_budge',
        'user_dbs_check',
        'user_training_check'
    ],

    'date_format' => [
        'date' => 'd-m-Y',
        'time' => 'H:i',
        'date_time' => 'd-m-Y H:i:s'
    ],

    'search_date_format' => [ //$whereFormat = '%m/%d/%Y %h:%i %p';
        'date' => '%d-%m-%Y',
        'time' => '%H:%i',
        'date_time' => '%d-%m-%Y %H:%i:%s'
    ],

    'js_date_format' => [ //$whereFormat = '%m/%d/%Y %h:%i %p';
        'date' => 'dd-mm-yy',
        'time' => 'H:i',
        // 'date_time' => 'dd-mm-%Y %H:%i:%p'
    ],

    'shift_status' => [
        'open' => 'Open', 
        'picked' => 'Picked', 
        'cancel' => 'Cancel', 
        'complete' => 'Complete'
    ],

    'ratings' =>[
        1 => '1 Star',
        2 => '2 Star',
        3 => '3 Star',
        4 => '4 Star',
        5 => '5 Star',
    ],

    'timepicker_step' => 15,
    'timepicker_min_time' => '00:00',
    'timepicker_max_time' => '24:00',

    'notification_subject' => [
        'help_chat'     => 'Help Chat', 
        'announcements' => 'Announcements',
    ],
];