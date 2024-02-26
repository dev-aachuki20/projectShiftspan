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

    'shop_description_length' => 200, 
    'travel_info_length' => 200, 

    'staff_file_fields' => [
        'user_dbs_certificate',
        'user_cv',
        'user_training_doc',
        'user_staff_budge',
        'user_dbs_check',
        'user_training_check'
    ],
]



?>
