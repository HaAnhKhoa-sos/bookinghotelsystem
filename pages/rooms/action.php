<?php
$pageTitle = 'Danh sách phòng | HotelBooking';
$roomTypes = getPublicRoomTypeOptions();
$filters = array(
    'min_price' => validPriceFilter(isset($_GET['min_price']) && is_string($_GET['min_price']) ? $_GET['min_price'] : ''),
    'max_price' => validPriceFilter(isset($_GET['max_price']) && is_string($_GET['max_price']) ? $_GET['max_price'] : ''),
);
$selectedRoomTypeId = null;
if (isset($_GET['room_type_id']) && is_string($_GET['room_type_id']) && ctype_digit($_GET['room_type_id'])) {
    $requestedRoomTypeId = (int) $_GET['room_type_id'];
    if ($requestedRoomTypeId > 0 && getPublicRoomTypeById($requestedRoomTypeId)) {
        $selectedRoomTypeId = $requestedRoomTypeId;
    }
}
$rooms = getPublicRooms(null, $selectedRoomTypeId, false, $filters['min_price'], $filters['max_price']);
$hasFilters = $selectedRoomTypeId !== null || $filters['min_price'] !== null || $filters['max_price'] !== null;
