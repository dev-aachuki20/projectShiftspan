<?php

return [
    'userManagement' => [
        'title'          => 'User Management',
        'title_singular' => 'User Management',
    ],
    'user'           => [
        'title'          => 'Users',
        'title_singular' => 'User',
        'fields'         => [
            'id'                       => 'ID',
            'first_name'               => 'First Name',
            'last_name'                => 'Last Name',
            'name'                     => 'Name',
            'full_name'                => 'Full name',
            'email'                    => 'Email',
            'phone'                    => 'Phone Number',
            'profile_image'            => 'Profile Image',
            'status'                   => 'Status',
            'password'                 => 'Password',
            'confirm_password'         => 'Password Confirm',
            'role'                     => 'User Level',
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
            'rating'                   => 'Rating',
        ],
        // admin profile
        'admin_profile'     => [
            'title'          => 'Profile',
            'fields'         => [
                'admin_name'               => 'Admin Name',
                'mobile'                   => 'Mobile',
                'image'                    => 'Image',
            ],
        ],   
    ],
    

    'permission'     => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'         => [
            'id'                => 'ID',
            'title'             => 'Title',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ],
    ],

    'location' => [
        'title' => 'Locations',
        'title_singular' => 'Location',
        'fields' => [
            'name'              => 'Name',
            'status'            => 'Status',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ]
    ],

    'occupation' => [
        'title' => 'Occupations',
        'title_singular' => 'Occupation',
        'fields' => [
            'id'                => 'ID',
            'name'              => 'Name',
            'occupation_name'   => 'Occupation Name',
            'status'            => 'Status',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ]
    ],

    'setting' => [
        'title' => 'Settings',
        'title_singular' => 'Setting',
        'fields' => [
            'site_title'    => 'Title Name',
            'site_logo'     => 'Logo Image',
            'icon_image'    => 'Icon Image',
            'change_logo'   => 'Change Logo',
            'help_pdf'      => 'Upload Help PDF',
        ],
        'contact_details' => [
            'title' => 'Contact Us',
            'fields' => [
                'contact_email' => 'Contact Email',
                'contact_phone' => 'Contact Mobile',
                'contact_details' => 'Contact Details',
            ]
        ],
    ],
    'datatable' => [
        'show' => 'Show',
        'entries' => 'entries',
        'showing' => 'Showing',
        'to' => 'To',
        'of'    => 'Of',
        'search' => 'Search',
        'previous' => 'Previous',
        'next' => 'Next',
        'first' => 'First',
        'last'  => 'Last',
        'data_not_found' => 'Data not found',
        'processing'   => 'Processing...',
    ],

    'client_admin' => [
        'title' => 'Client Admins',
        'title_singular' => 'Client Admin',
        'fields' => [
            'id'                => 'ID',
            'name'              => 'Client Name',
            'email'             => 'Email',
            'status'            => 'Status',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ]
    ],
];
