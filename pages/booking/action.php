<?php
$pageTitle = 'Đặt phòng | HotelBooking';
$bookingErrors = array();
$bookingValues = array('check_in' => '', 'check_out' => '', 'guest_count' => '1');
$roomId = isset($_GET['room_id']) && is_string($_GET['room_id'])
    ? filter_var($_GET['room_id'], FILTER_VALIDATE_INT)
    : false;
$bookingRoom = $roomId ? getBookingRoom($roomId) : null;

if (!$bookingRoom) {
    http_response_code(404);
    $bookingErrors[] = 'Không tìm thấy phòng cần đặt.';
} elseif (!current_customer_id()) {
    $_SESSION['after_login'] = 'booking';
    $_SESSION['after_login_room_id'] = (int) $roomId;
    set_flash('notice', 'Vui lòng đăng nhập hoặc tạo tài khoản để tiếp tục đặt phòng.');
    redirect(url('login'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bookingRoom && current_customer_id()) {
    if (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $bookingErrors[] = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
    } else {
        foreach ($bookingValues as $field => $default) {
            $bookingValues[$field] = isset($_POST[$field]) && is_string($_POST[$field])
                ? trim($_POST[$field])
                : '';
        }
        $guestCount = filter_var($bookingValues['guest_count'], FILTER_VALIDATE_INT);
        $checkInDate = preg_match('/^\d{4}-\d{2}-\d{2}$/D', $bookingValues['check_in'])
            ? DateTime::createFromFormat('!Y-m-d', $bookingValues['check_in'])
            : false;
        $checkOutDate = preg_match('/^\d{4}-\d{2}-\d{2}$/D', $bookingValues['check_out'])
            ? DateTime::createFromFormat('!Y-m-d', $bookingValues['check_out'])
            : false;
        $today = date('Y-m-d');

        if (
            !$checkInDate || $checkInDate->format('Y-m-d') !== $bookingValues['check_in']
            || $bookingValues['check_in'] < $today
        ) {
            $bookingErrors[] = 'Ngày nhận phòng phải là ngày hợp lệ, kể từ hôm nay.';
        }
        if (
            !$checkOutDate || $checkOutDate->format('Y-m-d') !== $bookingValues['check_out']
            || ($checkInDate && $checkOutDate <= $checkInDate)
        ) {
            $bookingErrors[] = 'Ngày trả phòng phải hợp lệ và sau ngày nhận phòng.';
        }
        if (!$guestCount || $guestCount < 1 || $guestCount > (int) $bookingRoom['capacity']) {
            $bookingErrors[] = 'Số khách phải từ 1 đến ' . (int) $bookingRoom['capacity'] . '.';
        }
        if ($bookingRoom['status'] !== 'available') {
            $bookingErrors[] = 'Phòng hiện không thể đặt.';
        } elseif ($checkInDate && $checkOutDate && $checkOutDate > $checkInDate
            && !roomIsAvailableForDates($roomId, $bookingValues['check_in'], $bookingValues['check_out'])) {
            $bookingErrors[] = 'Phòng đã có đơn đặt trong khoảng ngày này. Vui lòng chọn ngày khác.';
        }

        if (!$bookingErrors) {
            $createdBookingId = createCustomerBooking(
                current_customer_id(),
                $roomId,
                $bookingValues['check_in'],
                $bookingValues['check_out'],
                $guestCount
            );
            if (!$createdBookingId) {
                $bookingErrors[] = 'Phòng vừa được đặt hoặc không còn khả dụng. Vui lòng tải lại và chọn ngày khác.';
            } else {
                set_flash('success', 'Yêu cầu đặt phòng đã được gửi và đang chờ xác nhận.');
                redirect(url('my-bookings'));
            }
        }
    }
}
