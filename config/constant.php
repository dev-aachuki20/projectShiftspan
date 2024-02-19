<?php

return [
    'default' => [
        'logo' => 'default/logo.png',
        'favicon' => 'default/favicon.png',
        'no_image' => 'default/no-image.jpg',
        'staff-image' => 'default/staff-img.png',
        'help_pdf' => 'default/help_pdf.pdf',
        'user_icon' => 'default/user-icon.svg',
    ],
    'profile_max_size' => 2048,
    'profile_max_size_in_mb' => '2MB',

    'roles' =>[
        'super_admin' => 1,
        'admin' => 2,
        'staff' => 3,
    ],


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
