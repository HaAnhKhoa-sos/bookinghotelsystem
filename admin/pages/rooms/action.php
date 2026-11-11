<?php
$requestedAction = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'create')
    : (isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '');
$roomId = isset($_GET['id']) && is_string($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;
$editingRoom = null;
$showRoomForm = in_array($requestedAction, array('create', 'edit'), true);
$formTitle = $requestedAction === 'edit' ? 'Chỉnh sửa phòng' : 'Thêm phòng';
$errors = array();
$adminActionError = '';

if ($requestedAction === 'edit') {
    if (!$roomId) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy phòng cần chỉnh sửa.';
        $showRoomForm = false;
    } else {
        $editingRoom = getAdminRoomById($roomId);
        if (!$editingRoom) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy phòng cần chỉnh sửa.';
            $showRoomForm = false;
        }
    }
}

$formValues = array(
    'room_number' => $editingRoom ? $editingRoom['room_number'] : '',
    'room_name' => $editingRoom ? $editingRoom['room_name'] : '',
    'room_type_id' => $editingRoom ? (string) $editingRoom['room_type_id'] : '',
    'price' => $editingRoom ? (string) $editingRoom['price'] : '',
    'status' => $editingRoom ? $editingRoom['status'] : 'available',
    'bed_description' => $editingRoom ? $editingRoom['bed_description'] : '',
    'image_url' => $editingRoom ? $editingRoom['image_url'] : '',
    'is_featured' => $editingRoom ? (string) $editingRoom['is_featured'] : '0',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $csrfError = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        if (in_array($requestedAction, array('create', 'edit'), true)) {
            $errors[] = $csrfError;
        } else {
            $adminActionError = $csrfError;
        }
    } elseif ($requestedAction === 'delete') {
        if (!$roomId || !getAdminRoomById($roomId)) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy phòng cần xóa.';
        } elseif (roomHasBookings($roomId)) {
            $adminActionError = 'Không thể xóa phòng đã có lịch sử đặt phòng.';
        } else {
            deleteRoom($roomId);
            redirect(admin_url('rooms', array('success' => 'deleted')));
        }
    } elseif ($requestedAction === 'edit' && (!$roomId || !getAdminRoomById($roomId))) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy phòng cần chỉnh sửa.';
        $showRoomForm = false;
    } elseif (!in_array($requestedAction, array('create', 'edit'), true)) {
        http_response_code(400);
        $adminActionError = 'Thao tác quản lý phòng không hợp lệ.';
    } else {
        $showRoomForm = true;
        foreach ($formValues as $field => $default) {
            $formValues[$field] = $field === 'is_featured' ? '0' : '';
        }
        foreach ($formValues as $field => $default) {
            if ($field === 'is_featured' && !array_key_exists($field, $_POST)) {
                $formValues[$field] = '0';
            } elseif (isset($_POST[$field]) && is_string($_POST[$field])) {
                $formValues[$field] = trim($_POST[$field]);
            } elseif (array_key_exists($field, $_POST)) {
                $errors[] = 'Dữ liệu gửi lên không hợp lệ.';
            }
        }

        $validStatuses = array('available', 'occupied', 'maintenance');
        $roomTypeId = filter_var($formValues['room_type_id'], FILTER_VALIDATE_INT);
        $roomPrice = validPriceFilter($formValues['price']);
        if ($formValues['room_number'] === '' || strlen($formValues['room_number']) > 20) {
            $errors[] = 'Số phòng là bắt buộc và không vượt quá 20 ký tự.';
        } elseif (roomNumberExists($formValues['room_number'], $requestedAction === 'edit' ? $roomId : null)) {
            $errors[] = 'Số phòng này đã tồn tại.';
        }
        if ($formValues['room_name'] === '' || strlen($formValues['room_name']) > 100) {
            $errors[] = 'Tên phòng là bắt buộc và không vượt quá 100 ký tự.';
        }
        if (!$roomTypeId || !getPublicRoomTypeById($roomTypeId)) {
            $errors[] = 'Vui lòng chọn loại phòng hợp lệ.';
        }
        if ($roomPrice === null) {
            $errors[] = 'Vui lòng nhập giá phòng hợp lệ (tối đa 2 chữ số thập phân).';
        }
        if (!in_array($formValues['status'], $validStatuses, true)) {
            $errors[] = 'Trạng thái phòng không hợp lệ.';
        }
        if (!in_array($formValues['is_featured'], array('0', '1'), true)) {
            $errors[] = 'Trạng thái phòng nổi bật không hợp lệ.';
        }
        if (strlen($formValues['bed_description']) > 100) {
            $errors[] = 'Thông tin giường không vượt quá 100 ký tự.';
        }
        if ($formValues['image_url'] !== '') {
            $imageUrlParts = parse_url($formValues['image_url']);
            if (
                strlen($formValues['image_url']) > 1000
                || !filter_var($formValues['image_url'], FILTER_VALIDATE_URL)
                || !is_array($imageUrlParts)
                || !isset($imageUrlParts['scheme'])
                || !in_array(strtolower($imageUrlParts['scheme']), array('http', 'https'), true)
            ) {
                $errors[] = 'Đường dẫn hình ảnh phải là URL HTTP/HTTPS hợp lệ và tối đa 1000 ký tự.';
            }
        }

        if (!$errors) {
            $room = array(
                'room_number' => $formValues['room_number'],
                'room_name' => $formValues['room_name'],
                'room_type_id' => $roomTypeId,
                'price' => $roomPrice,
                'status' => $formValues['status'],
                'bed_description' => $formValues['bed_description'],
                'image_url' => $formValues['image_url'],
                'is_featured' => (int) $formValues['is_featured'],
            );
            if ($requestedAction === 'edit') {
                updateRoom($roomId, $room);
                redirect(admin_url('rooms', array('success' => 'updated')));
            }

            createRoom($room);
            redirect(admin_url('rooms', array('success' => 'created')));
        }
    }
}
