<?php

return [
    'datatable' => [
        'show' => 'Show',
        'entries' => 'entries',
        'showing' => 'Showing',
        'to' => 'to',
        'of'    => 'of',
        'search' => 'Search',
        'previous' => 'Previous',
        'next' => 'Next',
        'first' => 'First',
        'last'  => 'Last',
        'data_not_found' => 'Data not found',
        'processing'   => 'Processing...',
    ],

    'dashboard' =>  [
        'title'          => 'Dashboard',
        'title_singular' => 'Dashboard',
        'fields'         => [
            'register_staff'        =>  'Registered Staff',
            'total_shifts'          =>  'Total Shifts',
            'business_location'     =>  'Listed Businesses',
        ],
    ],

    'header' =>  [
        'title'          => 'Header',
        'title_singular' => 'Header',
        'fields'         => [
            'notifications'         =>  'Notifications/Alerts',
            'company_id'            =>  'Company ID',
            'help'                  =>  'Help',
        ],
    ],

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
            'username'                 => 'Username'
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
            'sub_admin'         => 'Client Admin',
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
            'sub_admin'         => 'Client Admin',
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
            'help_pdf'      => 'Help PDF',
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
    

    'client_admin' => [
        'title' => 'Client Admins',
        'title_singular' => 'Client Admin',
        'fields' => [
            'id'                => 'ID',
            'name'              => 'Name',
            'email'             => 'Email',
            'password'          => 'Password',
            'status'            => 'Status',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ]
    ],

    'client_detail' => [
        'title' => 'Listed Businesses',
        'title_singular' => 'Listed Business',
        'fields' => [
            'id'                => 'ID',
            'client_admin'       => 'Client Admin',
            'client_name'       => 'Client Name',
            'location_id'       => 'Location',
            'name'              => 'Name',
            'address'           => 'Address',
            'shop_description'  => 'Shops Nearby',
            'travel_info'       => 'Travel Info',
            'building_image'    => 'Building Image',
            'status'            => 'Status',
            'client_admin_name' => 'Client Admin Name',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ]
    ],

    'staff' => [
        'title' => 'Staffs',
        'title_singular' => 'Staff',
        'fields' => [
            'id'                            => 'ID',
            'staff_admin'                   => 'Client Admin',
            'title'                         => 'Title',
            'name'                          => 'Staff Name',
            'username'                      => 'Username',
            'staff_image'                   => 'Staff Image',
            'email'                         => 'Email',
            'password'                      => 'Password',
            'phone'                         => 'Phone Number',
            'status'                        => 'Status',
            'set_status'                    => 'Set Status',
            'created_by'                    => 'Created By',
            'created_at'                    => 'Date',
            'updated_at'                    => 'Updated at',
            'deleted_at'                    => 'Deleted at',
            'dob'                           => 'Date of Birth',
            'previous_name'                 => 'Previous Name',
            'national_insurance_number'     => 'National Insurance Number',
            'address'                       => 'Address',
            'education'                     => 'Education',
            'prev_emp_1'                    => 'Previous Employments 1',
            'prev_emp_2'                    => 'Previous Employments 2',
            'reference_1'                   => 'References 1',
            'reference_2'                   => 'References 2',
            'date_sign'                     => 'Date Sign',
            'criminal'                      => 'Criminal Conviction',
            'rehabilitation_of_offenders'   => 'Rehabilitation Of Offenders',
            'enquires'                      => 'Enquires',
            'health_issue'                  => 'Health Issues',
            'statement'                     => 'Statement',
            'relevant_training_image'       => 'Relevant Training Documents',
            'dbs_certificate'               => 'DBS Certificate',
            'cv'                            => 'CV',
            'staff_budge'                   => 'Other',
            'dbs_check'                     => 'Other',
            'training_check'                => 'Other',
            'staff_rating'                  => 'Staff Rating',
        ]
    ],

    'notification' => [
        'title' => 'Notifications',
        'title_singular' => 'Notification',
        'fields' => [
            'id'                            => 'ID',
            'notification_settings'         => 'Notification Settings',
            'staff'                         => 'Staff',
            'all_staff'                     => 'ALL STAFF',
            'section'                       => 'Section',
            'subject'                       => 'Subject',
            'message'                       => 'Message',
            'type'                          => 'Type',
            'type_message'                  => 'Type Message',
            'new_message'                   => 'New Message',
        ]
    ],

    'message' => [
        'title' => 'Messages',
        'title_singular' => 'Message',
        'fields' => [
            'id'                            => 'ID',
            'message_center'                => 'Message Centre',
            'new_message'                   => 'New Message',
            'delete_message'                => 'Delete Message',
            'section'                       => 'Section',
            'subject'                       => 'Subject',
            'message'                       => 'Message',
            'sent'                          => 'Sent To',
            'received'                      => 'Received From',
        ]
    ],

    'shift' => [
        'title' => 'Shifts',
        'title_singular' => 'Shift',
        'fields' => [
            'id'                => 'ID',
            'shift_label'      => 'Shift Label',
            'client_name'      => 'Client Admin',
            'client_detail_name'=> 'Listed Business Name',
            'staff_name'        => 'Staff Name',
            'start_date'        => 'Start Date',
            'end_date'          => 'End Date',
            'start_time'        => 'Start Time',
            'end_time'          => 'End Time',
            'picked_at'         => 'Picked Time',
            'cancel_at'         => 'Time Cancelled',
            'rating'            => 'Rating',
            'occupation_id'     => 'Occupation',
            'assign_staff'      => 'Assign Staff',
            'quantity'          => 'Quantity',
            'status'            => 'Status',
            'clock_in'          => 'Clock In',
            'clock_out'         => 'Clock Out',
            'timesheet'         => 'Timesheet',
            'created_by'        => 'Created By',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',

            'manager_name'      => 'Manager Name',
            'clock_in_time'     => 'Clock In Time',
            'clock_out_time'    => 'Clock Out Time',
            'geolocation'       => 'Geolocation',
            'manager_signature' => 'Manager Signature'
        ]
    ],
];
