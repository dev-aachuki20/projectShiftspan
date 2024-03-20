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
        'shift_created_and_assign_subject'      => 'Shift Assign',
        'shift_created_and_assign_message'      => 'Hello :username, We are pleased to inform you that the shift has been successfully created and assigned for the following duration from :start_date to :end_date and :start_time to :end_time.',

        'shift_picked_subject'                  => 'Shift Picked',
        'shift_picked_admin_message'            => 'User :username has :status the shift :picked_at.',

        'shift_picked_update_subject'           => 'Shift Picked Update Notification',
        'shift_picked_update_message'           => 'Hi :username, your shift has been picked up by another staff member. Thank you for your service.',

        'shift_delete_subject'                  => 'Shift Delete Notification',
        'shift_delete_message'                  => 'Hi :username, your shift has been deleted. Please contact your manager for further information.',

        'shift_completed_subject'               => 'Shift Completed',
        'shift_completed_admin_message'         => 'User :username has :status the shift :completed_at.',

        'shift_clock_in_subject'                => 'Clock In',
        'shift_clock_in_admin_message'          => ':username has clocked in for the shift at :clockin_date :clockin_time.',
        
        'shift_clock_out_subject'               => 'Clock Out',
        'shift_clock_out_admin_message'         => ':username has clocked out from the shift at :clockout_date :clockout_time.',

        'shift_authorised_sign_subject'         => 'Shift Authorised Sign',
        'shift_authorised_sign_admin_message'   => ':username has been authorised by :manager_name for the shift at :authorize_at :authorize_time.',

        'shift_rating_subject'                  => 'Shift Rating Notification',
        'shift_rating_message'                  => 'Hi :username, your shift has been rated :rating star by the :admin. Thank you for your service.',

    ],

    'registration_completion_subject'           => 'Completed successfully. Thank you for registering your account.',
    'registration_completion_message'           => 'Hi :username, Congratulations! Your account has been approved by the :admin. Welcome to our service. Thank you!',
    
    'registration_completion_admin_subject'     => 'New User Registration',
    'registration_completion_admin_message'     => 'A new user :username has completed registration. Please review and approve their account.',

    'user_account_deactivate_subject'          => 'Your Account has been Deactivated',
    'user_account_deactivate_message'          => 'Hi :username, your account has been deactivated by :admin. If you believe this is a mistake, please contact support.',
];
