<?php

return [
    'userManagement' => [
        'title'          => 'User Management',
        'title_singular' => 'User Management',
    ],
    'setting' => [
        'title'          => 'Settings',
        'title_singular' => 'Setting',
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
            'id'                => 'ID',
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
    ]

];
