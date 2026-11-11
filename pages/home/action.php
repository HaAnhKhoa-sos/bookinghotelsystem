<?php
$selectedRoomTypeId = null;
$minPrice = validPriceFilter(isset($_GET['min_price']) && is_string($_GET['min_price']) ? $_GET['min_price'] : '');
$maxPrice = validPriceFilter(isset($_GET['max_price']) && is_string($_GET['max_price']) ? $_GET['max_price'] : '');
if (isset($_GET['room_type_id']) && is_string($_GET['room_type_id']) && ctype_digit($_GET['room_type_id'])) {
    $requestedRoomTypeId = (int) $_GET['room_type_id'];
    if ($requestedRoomTypeId > 0 && getPublicRoomTypeById($requestedRoomTypeId)) {
        $selectedRoomTypeId = $requestedRoomTypeId;
    }
}

$roomTypes = getPublicRoomTypeOptions();
$featuredRooms = getFeaturedRooms(3);
$rooms = getPublicRooms(6, $selectedRoomTypeId, false, $minPrice, $maxPrice);
$hasRoomFilters = $selectedRoomTypeId !== null || $minPrice !== null || $maxPrice !== null;
