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
?>
<main>
  <section class="page-header room-list-header">
    <div class="container"><p class="eyebrow">Không gian nghỉ dưỡng</p><h1>Chọn căn phòng dành cho bạn</h1><p>Tìm nơi nghỉ phù hợp cho chuyến đi tiếp theo.</p></div>
  </section>
  <section class="container page-content">
    <div class="room-listing">
      <form class="home-room-filter" action="<?= e(url('rooms')) ?>" method="get">
        <label for="rooms-room-type">Lọc theo loại phòng</label>
        <select id="rooms-room-type" name="room_type_id">
          <option value="">Tất cả loại phòng</option>
          <?php foreach ($roomTypes as $roomType): ?>
            <option value="<?= e($roomType['id']) ?>" <?= $selectedRoomTypeId === (int) $roomType['id'] ? 'selected' : '' ?>><?= e($roomType['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="rooms-min-price">Giá từ
          <input id="rooms-min-price" type="number" name="min_price" min="0" max="9999999999.99" step="0.01" value="<?= e($filters['min_price'] === null ? '' : $filters['min_price']) ?>" placeholder="Tối thiểu" />
        </label>
        <label for="rooms-max-price">Đến
          <input id="rooms-max-price" type="number" name="max_price" min="0" max="9999999999.99" step="0.01" value="<?= e($filters['max_price'] === null ? '' : $filters['max_price']) ?>" placeholder="Tối đa" />
        </label>
        <button class="btn btn-primary" type="submit">Lọc phòng</button>
        <?php if ($hasFilters): ?><a class="text-link" href="<?= e(url('rooms')) ?>">Xóa bộ lọc</a><?php endif; ?>
      </form>
      <div class="listing-heading"><h2>Danh sách phòng</h2><span><?= e(count($rooms)) ?> phòng</span></div>
      <div class="room-grid">
        <?php foreach ($rooms as $room): ?>
          <article class="room-card">
            <a class="room-card-image" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>">
              <img class="room-image" src="<?= e($room['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=85') ?>" alt="<?= e($room['room_name']) ?>" />
            </a>
            <div class="body">
              <p class="eyebrow">Phòng <?= e($room['room_number']) ?> · <?= e($room['room_type_name']) ?></p>
              <h3><a class="room-title-link" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>"><?= e($room['room_name']) ?></a></h3>
              <div class="meta"><span><?= e($room['capacity']) ?> khách · <?= e($room['area_sqm']) ?> m²</span><span class="price"><?= e(money($room['price'])) ?> / đêm</span></div>
              <p><?= e($room['description']) ?></p>
              <p class="room-status room-status-<?= e($room['status']) ?>"><?= $room['status'] === 'available' ? 'Có thể đặt' : ($room['status'] === 'occupied' ? 'Đang sử dụng' : 'Đang bảo trì') ?></p>
              <a class="btn btn-primary" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>">Xem chi tiết</a>
            </div>
          </article>
        <?php endforeach; ?>
        <?php if (!$rooms): ?>
          <p>Không tìm thấy phòng phù hợp với bộ lọc.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
