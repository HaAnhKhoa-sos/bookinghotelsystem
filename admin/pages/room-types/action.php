<?php
$requestedAction = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
$roomTypeId = isset($_GET['id']) && is_string($_GET['id'])
    ? filter_var($_GET['id'], FILTER_VALIDATE_INT)
    : false;
$editingRoomType = null;
$showRoomTypeForm = in_array($requestedAction, array('create', 'edit'), true);
$roomTypeFormTitle = $requestedAction === 'edit' ? 'Chỉnh sửa loại phòng' : 'Thêm loại phòng';
$roomTypeErrors = array();
$adminActionError = '';

if ($requestedAction === 'edit') {
    $editingRoomType = $roomTypeId ? getAdminRoomTypeById($roomTypeId) : null;
    if (!$editingRoomType) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy loại phòng cần chỉnh sửa.';
        $showRoomTypeForm = false;
    }
}

$roomTypeValues = array(
    'name' => $editingRoomType ? $editingRoomType['name'] : '',
    'description' => $editingRoomType ? $editingRoomType['description'] : '',
    'capacity' => $editingRoomType ? (string) $editingRoomType['capacity'] : '',
    'area_sqm' => $editingRoomType ? (string) $editingRoomType['area_sqm'] : '',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        if (in_array($requestedAction, array('create', 'edit'), true)) {
            $roomTypeErrors[] = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
        } else {
            $adminActionError = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
        }
    } elseif ($requestedAction === 'delete') {
        if (!$roomTypeId || !getAdminRoomTypeById($roomTypeId)) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy loại phòng cần xóa.';
        } elseif (roomTypeHasRooms($roomTypeId)) {
            $adminActionError = 'Không thể xóa loại phòng đang được sử dụng bởi phòng hiện có.';
        } else {
            deleteRoomType($roomTypeId);
            redirect(admin_url('room-types', array('success' => 'deleted')));
        }
    } elseif ($requestedAction === 'edit' && (!$roomTypeId || !getAdminRoomTypeById($roomTypeId))) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy loại phòng cần chỉnh sửa.';
        $showRoomTypeForm = false;
    } elseif (!in_array($requestedAction, array('create', 'edit'), true)) {
        http_response_code(400);
        $adminActionError = 'Thao tác quản lý loại phòng không hợp lệ.';
    } else {
        $showRoomTypeForm = true;
        foreach ($roomTypeValues as $field => $default) {
            $roomTypeValues[$field] = isset($_POST[$field]) && is_string($_POST[$field])
                ? trim($_POST[$field])
                : '';
        }
        $capacity = filter_var($roomTypeValues['capacity'], FILTER_VALIDATE_INT);
        $area = validPriceFilter($roomTypeValues['area_sqm']);

        if ($roomTypeValues['name'] === '' || strlen($roomTypeValues['name']) > 100) {
            $roomTypeErrors[] = 'Tên loại phòng là bắt buộc và không vượt quá 100 ký tự.';
        } elseif (roomTypeNameExists($roomTypeValues['name'], $requestedAction === 'edit' ? $roomTypeId : null)) {
            $roomTypeErrors[] = 'Tên loại phòng này đã tồn tại.';
        }
        if ($roomTypeValues['description'] === '' || strlen($roomTypeValues['description']) > 5000) {
            $roomTypeErrors[] = 'Mô tả là bắt buộc và không vượt quá 5000 ký tự.';
        }
        if (!$capacity || $capacity < 1 || $capacity > 255) {
            $roomTypeErrors[] = 'Sức chứa phải từ 1 đến 255 khách.';
        }
        if ($area === null || (float) $area <= 0 || (float) $area >= 10000) {
            $roomTypeErrors[] = 'Diện tích phải lớn hơn 0 và nhỏ hơn 10000 m².';
        }

        if (!$roomTypeErrors) {
            $roomType = array(
                'name' => $roomTypeValues['name'],
                'description' => $roomTypeValues['description'],
                'capacity' => $capacity,
                'area_sqm' => $area,
            );
            if ($requestedAction === 'edit') {
                updateRoomType($roomTypeId, $roomType);
                redirect(admin_url('room-types', array('success' => 'updated')));
            }

            createRoomType($roomType);
            redirect(admin_url('room-types', array('success' => 'created')));
        }
    }
}
