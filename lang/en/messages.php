<?php
return [
    'validation_error' => 'Validation error',
    'not_found' => 'Not found',
    'access_denied' => "You do not have enough permissions. Access is denied",
    'system_error' => 'System error.',
    'unauthenticated' => 'unauthenticated.',
    'no_data' => 'No data need to update.',
    'role' => [
        'create_successfully' => 'Create new role successfully',
        'create_fail' => 'Create new role fail',
        'role_existed' => 'Role name is existed',
        'role_not_found' => 'Role is not existed',
        'update_successfully' => 'Update role successfully',
        'update_fail' => 'Update role fail',
        'delete_successfully' => 'Delete role successfully',
        'restore_successfully' => 'Restore role successfully',
        'restore_fail' => 'Restore role fail',
        'delete_fail' => 'Delete role fail',
        'give_permissions_successfully' => 'Give permissions to role successfully',
        'give_permissions_fail' => 'Give permissions to role fail',
        'delete_has_data_depend' => 'Role has user assigned',
    ],
    'hierarchy' => [
        'create_successfully' => 'Create new unit successfully',
        'create_fail' => 'Create new unit fail',
        'update_successfully' => 'Update unit information successfully',
        'update_fail' => 'Update unit information fail',
        'not_valid_company' => 'Company is not valid',
        'delete_successfully' => 'Delete unit successfully',
        'delete_fail' => 'Delete unit fail',
        'can_not_delete' => 'Can not delete unit',
    ],
    'appointment' => [
        'guest_information_invalid' => 'Guest information is invalid',
        'status_invalid' => 'Status is invalid',
    ],
    'employee' => [
        'electronic_record' => [
            'create_successfully' => 'Create electronic record successfully',
            'create_fail' => 'Create electronic fail',
        ],
        'health' => [
            'update_successfully' => 'Update health record successfully',
            'update_fail' => 'Update health record fail',
        ]
    ],
    'change_fail' => 'Old Password is Incorrect',
    'change_successfully' => 'Change Password Successfully',
    'media_required' => 'Media file is required',
    'log' => [
        'create' => 'Created',
        'update' => 'Updated',
        'delete' => 'Deleted',
        'old_value' => [
            'status' => [
                'approved' => 'Approved',
                'processing' => 'Processing',
                'completed' => 'Completed',
                'rejected' => 'Rejected',
                'pending' => 'Pending',
            ]
        ],
        'new_value' => [
            'status' => [
                'approved' => 'Approved',
                'processing' => 'Processing',
                'completed' => 'Completed',
                'rejected' => 'Rejected',
                'pending' => 'Pending',
            ]
        ]
    ],
    'send_email_successfully' => 'Send Email Successfully',
    'send_email_fail' => 'Send Email Fail',
    'reset_successfully' => 'Reset Password Successfully',
    'token_invalid' => 'This password reset token is invalid.',
    'asset' => [
        'update_successfully' => 'Update asset record successfully',
        'update_fail' => 'Update asset record fail',
    ],
    'collections' => [
        'identification_front' => 'Identification Front',
        'identification_back' => 'Identification Back',
        'face_image' => 'Face Image',
        'fingerprint' => 'Fingerprint',
        'job_transfer_proofs' => 'Job Transfer Proofs',
        'contract_files' => 'Contract Files',
        'contract_health_records' => 'Contract Health Records',
        'health_records' => 'Health Records',
    ]
];
