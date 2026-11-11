<?php
$showRoomForm = ($_SERVER['REQUEST_METHOD'] === 'POST') || (($_GET['action'] ?? '') === 'create');
$formValues = array(
    'room_number' => '',
    'room_name' => '',
    'room_type_id' => '',
    'price' => '',
    'status' => $_SERVER['REQUEST_METHOD'] === 'POST' ? '' : 'available',
    'bed_description' => '',
    'image_url' => '',
    'is_featured' => '0',
);
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formValues as $field => $default) {
        if (isset($_POST[$field]) && is_string($_POST[$field])) {
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
    } elseif (roomNumberExists($formValues['room_number'])) {
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
        createRoom(array(
            'room_number' => $formValues['room_number'],
            'room_name' => $formValues['room_name'],
            'room_type_id' => $roomTypeId,
            'price' => $roomPrice,
            'status' => $formValues['status'],
            'bed_description' => $formValues['bed_description'],
            'image_url' => $formValues['image_url'],
            'is_featured' => (int) $formValues['is_featured'],
        ));
        redirect(admin_url('rooms', array('success' => 'created')));
    }
}
