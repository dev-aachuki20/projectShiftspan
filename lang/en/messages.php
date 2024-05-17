<?php

return [

    'crud'=>[
        'add_record'    => 'Successfully Added !',
        'update_record' => 'Successfully Updated !',
        'delete_record' => 'This record has been succesfully deleted!',
        'restore_record'=> 'This record has been succesfully Restored!',
        'merge_record'  => 'This record has been succesfully Merged!',
        'approve_record'=> 'Record Successfully Approved !',
        'status_update' => 'Status successfully updated!',
        'notification_sent' => 'Notification Successfully sent!',
        'message_sent' => 'Message Successfully sent!',
    ],

    'unable_to_add_blank_field' => 'Sorry, Unable to add a blank field in',
    'data_already_exists' => 'Sorry, You cannot create new with the same name so use existing.',

    'areYouSure'=>'Are you sure you want to delete this record?',
    'areYouSureapprove'=>'Are you sure you want to Approve this record?',
    'areYouSurerestore'=>'Are you sure you want to Restore this Database? It will delete your current database.',
    'deletetitle'=>'Delete Confirmation',
    'restoretitle'=>'Restore Confirmation',
    'approvaltitle'=>'Approval Confirmation',
    'areYouSureRestore'=>'Are you sure you want to restore this record?',
    'error_message'   => 'Something went wrong....please try again later!',
    'no_record_found' => 'No Records Found!',
    'suspened'=> "Your account has been suspened!",
    'invalid_email'=>'Invalid Email',
    'invalid_otp'=>'Invalid OTP',
    'invalid_pin'=>'Invalid PIN',
    'wrong_credentials'=>'These credentials do not match our records!',
    'not_activate'=>'Your account is not activated.',
    'otp_sent_email'=>'We have successfully sent OTP on your Registered Email',
    'expire_otp'=> 'OTP has been Expired',
    'verified_otp'=> 'OTP successfully Verified.',
    'invalid_token_email'=> 'Invalid Token or Email!',
    'success'=>'Success',
    'register_success'=>'Your account created successfully! Please wait for the approval!',
    'login_success'=>'You have logged in successfully!',
    'logout_success'=>'Logged out successfully!',
    'warning_select_record'=> 'Please select at least one record',
    'required_role'=> "User with the specified email doesn't have the required role.",
    
    'invalid_token'                 => 'Your access token has been expired. Please login again.',
    'not_authorized'                => 'Not Authorized to access this resource/api',
    'not_found'                     => 'Not Found!',
    'endpoint_not_found'            => 'Endpoint not found',
    'resource_not_found'            => 'Resource not found',
    'token_invalid'                 => 'Token is invalid',
    'unexpected'                    => 'Unexpected Exception. Try later',
    
    'data_retrieved_successfully'   => 'Data retrieved successfully',
    'record_retrieved_successfully' => 'Record retrieved successfully',
    'record_created_successfully'   => 'Record created successfully',
    'record_updated_successfully'   => 'Record updated successfully',
    'record_deleted_successfully'   => 'Record deleted successfully',
    'password_updated_successfully' => 'Password updated successfully',

    'rating_shift'                  => 'Shift rated successfully',
    'profile_updated_successfully'  => 'Profile updated successfully',
    'shift_picked_success'          => 'Shift picked successfully',
    'clock_in_success'              => 'Clocked in successfully',
    'clock_out_success'             => 'Clocked out successfully',
    'shift_cancelled'               => 'Shift cancelled successfully',
    'authorized_shift_success'      => 'Shift authorized successfully',

    'account_deactivate'            => 'Your account has been deactivated. Please contact the admin.',
    'staff_account_deactivate'      => 'Your account has been deactivated.',

    'shift' =>[
        'shift_available_subject'               => 'New Shift Available',
        'shift_available_admin_message'         => ':shift_label shift available at :listed_business (:start_date Time: :start_time to :end_time).',
        'shift_available_staff_message'         => ':shift_label shift available at :listed_business (:start_date Time: :start_time to :end_time). Please pick it',

        'shift_created_and_assign_subject'      => 'Shift Assign',
        'shift_created_and_assign_message'      => 'Hello :username, We are pleased to inform you that the shift has been successfully created and assigned for the following duration from :start_date to :end_date and :start_time to :end_time.',

        'shift_picked_subject'                  => 'Shift Picked',
        'shift_picked_admin_message'            => ':username has :status the shift at :listed_business :picked_at.',

        'shift_picked_update_subject'           => 'Shift Picked Update Notification',
        'shift_picked_update_message'           => 'Hi :username, your shift has been picked up by another staff member. Thank you for your service.',

        'shift_delete_subject'                  => 'Shift Delete Notification',
        'shift_delete_message'                  => 'Hi :username, your shift has been deleted. Please contact your manager for further information.',

        'shift_completed_subject'               => 'Shift Completed',
        'shift_completed_admin_message'         => 'User :username has :status the shift :completed_at.',

        'shift_clock_in_subject'                => 'Shift Clock In',
        'shift_clock_in_admin_message'          => ':username has clocked in for the shift at :listed_business :clockin_date :clockin_time',
        
        'shift_clock_out_subject'               => 'Shift Clock Out',
        'shift_clock_out_admin_message'         => ':username has clocked out from the shift at :listed_business :clockout_date :clockout_time.',

        'shift_authorised_sign_subject'         => 'Shift Sign Off ',
        'shift_authorised_sign_admin_message'   => ':username’s shift :shift_label shift on :authorize_at at :listed_business has been signed off by manager :manager_name',

        'shift_rating_subject'                  => 'Shift Rating Notification',
        'shift_rating_message'                  => 'Hi :username, you have been rated :rating stars by :listed_business. Thank you for your service.',

        'shift_canceled_subject'               => 'Cancelled Shift',
        'shift_canceled_admin_message'         => ':username, your :shift_label shift at :listed_business on :cancelled_date has been cancelled. Please check your schedule for more info',
        'shift_canceled_staff_message'         => ':username, your :shift_label shift at :listed_business on :cancelled_date has been cancelled. Please check your schedule for more info',

        // shift reminder notification 
        'shift_clock_in_reminder_subject'       => 'CLOCK IN REMINDER ',
        'shift_clock_in_reminder_message'       => 'Your shift about to start soon. please remember to clock in',

        'shift_clock_out_reminder_subject'      => 'CLOCK OUT REMINDER',
        'shift_clock_out_reminder_message'      => 'Your shift about to end soon. please remember to clock out',
    ],

    'registration_completion_subject'           => 'Registration Complete',
    'registration_completion_message'           => 'Hi :username, Congratulations! Your registration is now complete and your account has been approved by :listed_business. Thank you for your patience. Please login.',
    
    'registration_completion_admin_subject'     => 'New Staff Registration',
    'registration_completion_admin_message'     => ':username has completed registration. Please review and activate their account',

    'user_account_deactivate_subject'          => 'Your Account has been Deactivated',
    'user_account_deactivate_message'          => 'Hi :username, your account has been deactivated by :admin. If you believe this is a mistake, please contact support.',

    'notification'=>[
        'not_found' => 'Notification not found',
        'mark_as_read' => 'Notification marked as read',
        'no_notification'=>'No notifications to clear!',
        'clear_notification' => 'All notifications have been cleared',
        'delete'             => 'Notification has been deleted successfully!',
    ]
];
