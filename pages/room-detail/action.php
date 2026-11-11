<?php
$roomId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$room = $roomId ? getPublicRoomById($roomId) : null;
if (!$room) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy phòng | HotelBooking';
} else {
    $pageTitle = $room['room_name'] . ' | HotelBooking';
}
