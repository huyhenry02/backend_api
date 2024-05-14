<?php
return [
    'validation_error' => 'Lỗi xác thực thông tin form',
    'not_found' => 'Không tìm thấy dữ liệu',
    'access_denied' => "Bạn không có quyền quyền thực hiện chức năng này.",
    'system_error' => 'Lỗi hệ thống',
    'unauthenticated' => 'Tài khoản không được xác thực.',
    'no_data' => 'Không có dữ liệu cần update.',
    'role' => [
        'create_successfully' => 'Tạo role thành công',
        'create_fail' => 'Tạo role thất bại',
        'role_existed' => 'Role đã tồn tại trong hệ thống',
        'role_not_found' => 'Role không tồn tại',
        'update_successfully' => 'Cập nhật thông tin role thành công',
        'update_fail' => 'Cập nhật thông tin role thất bại',
        'delete_successfully' => 'Xóa role thành công',
        'delete_fail' => 'Xóa role thất bại',
        'restore_successfully' => 'Phục hồi role thành công',
        'restore_fail' => 'Phục hồi role thất bại',
        'give_permissions_successfully' => 'Cập nhật quyền cho role thành công',
        'give_permissions_fail' => 'Cập nhật quyền cho role thất bại',
        'delete_has_data_depend' => 'Tồn tại tài khoản đang được gán nhóm quyền',
    ],
    'hierarchy' => [
        'create_successfully' => 'Tạo mới cơ cấu thành công',
        'create_fail' => 'Tạo mới cơ cấu thất bại',
        'update_successfully' => 'Cập nhật cơ cấu thành công',
        'update_fail' => 'Cập nhật cơ cấu thất bại',
        'not_valid_company' => 'Công ty không hợp lệ',
        'delete_successfully' => 'Xóa cơ cấu thành công',
        'delete_fail' => 'Xóa cơ cấu thất bại',
        'can_not_delete' => 'Công ty đang được sử dụng',
    ],
    'appointment' => [
        'guest_information_invalid' => 'Thông tin nguời hẹn không hợp lệ',
        'status_invalid' => 'Trạng thái không hợp lệ',
    ],
    'employee' => [
        'electronic_record' => [
            'create_successfully' => 'Tạo mới hồ sơ nhân sự thành công',
            'create_fail' => 'Tạo mới hồ sơ nhân sự thất bại',
        ],
        'health' => [
            'update_successfully' => 'Sửa hồ sơ y tế thành công',
            'update_fail' => 'Sửa hồ sơ y tế thất bại',
        ]
    ],
    'change_fail' => 'Mật khẩu cũ không đúng',
    'change_successfully' => 'Thay đổi mật khẩu thành công',
    'update_fail' => 'Cập nhật thất bại',
    'update_successfully' => 'Cập nhật thành công',
    'delete_fail' => 'Xóa thất bại',
    'delete_successfully' => 'Xóa thành công',
    'media_required' => 'File media không được bỏ trống',
    'log' => [
        'create' => 'Khởi tạo',
        'update' => 'Chỉnh sửa',
        'delete' => 'Xoá',
        'old_value' => [
            'status' => [
                'pending' => 'Chờ duyệt',
                'approved' => 'Đã duyệt',
                'processing' => 'Đang diễn ra',
                'completed' => 'Đã hoàn thành',
                'rejected' => 'Đã hủy',
            ]
        ],
        'new_value' => [
            'status' => [
                'pending' => 'Chờ duyệt',
                'approved' => 'Đã duyệt',
                'processing' => 'Đang diễn ra',
                'completed' => 'Đã hoàn thành',
                'rejected' => 'Đã hủy',
            ]
        ]
    ],
    'send_email_successfully' => 'Gửi Email thành công',
    'send_email_fail' => 'Gửi Email thất bại',
    'reset_successfully' => 'Thay đổi mật khẩu thành công',
    'token_invalid' => 'Mã thông báo đặt lại mật khẩu này không hợp lệ.',
    'asset' => [
        'update_successfully' => 'Sửa thiết bị thành công',
        'update_fail' => 'Sửa thiết bị thất bại',
    ],
    'collections' => [
        'identification_front' => 'Ảnh CCCD mặt trước',
        'identification_back' => 'Ảnh CCCD mặt sau',
        'face_image' => 'Ảnh khuôn mặt',
        'fingerprint' => 'Ảnh vân tay',
        'job_transfer_proofs' => 'Minh chứng chuyển công việc',
        'contract_files' => 'Hợp đồng',
        'contract_health_records' => 'Hồ sơ sức khỏe',
        'health_records' => 'Hồ sơ sức khỏe',
    ]
];
