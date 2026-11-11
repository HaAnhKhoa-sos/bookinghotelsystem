<?php
$pageTitle = 'HotelBooking | Kỳ nghỉ của bạn bắt đầu từ đây';
?>
<main>
  <section class="hero">
    <div class="container hero-content">
      <div>
        <span class="badge">Chào mừng đến với HotelBooking</span>
        <h1>Kỳ nghỉ của bạn bắt đầu từ đây</h1>
        <p>Không gian nghỉ dưỡng thoải mái, dịch vụ chu đáo và những lựa chọn phòng phù hợp cho mọi hành trình.</p>
        <div class="actions">
          <a class="btn btn-primary" href="<?= e(url('rooms')) ?>">Khám phá phòng</a>
          <a class="btn btn-secondary" href="#about">Về khách sạn</a>
        </div>
      </div>
    </div>
  </section>

  <section class="home-section container" id="about">
    <div class="section-heading">
      <p class="eyebrow">Về chúng tôi</p>
      <h2>Nơi nghỉ ngơi cho những chuyến đi đáng nhớ</h2>
    </div>
    <p class="section-intro">HotelBooking mang đến không gian lưu trú tiện nghi, vị trí thuận tiện và đội ngũ luôn sẵn sàng hỗ trợ.</p>
  </section>

  <section class="home-section home-section-tint">
    <div class="container">
      <div class="section-heading section-heading-row">
        <div><p class="eyebrow">Được tuyển chọn</p><h2>Phòng nổi bật</h2></div>
        <a class="text-link" href="<?= e(url('rooms')) ?>">Khám phá tất cả phòng</a>
      </div>
      <div class="room-grid">
        <?php foreach ($featuredRooms as $room): ?>
          <article class="room-card">
            <a class="room-card-image" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>">
              <img class="room-image" src="<?= e($room['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=85') ?>" alt="<?= e($room['room_name']) ?>" />
            </a>
            <div class="body">
              <p class="eyebrow">Phòng <?= e($room['room_number']) ?> · <?= e($room['room_type_name']) ?></p>
              <h3><?= e($room['room_name']) ?></h3>
              <div class="meta"><span><?= e($room['capacity']) ?> khách · <?= e($room['area_sqm']) ?> m²</span><span class="price"><?= e(money($room['price'])) ?> / đêm</span></div>
              <p class="room-status room-status-<?= e($room['status']) ?>"><?= $room['status'] === 'available' ? 'Có thể đặt' : ($room['status'] === 'occupied' ? 'Đang sử dụng' : 'Đang bảo trì') ?></p>
              <a class="text-link" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>">Xem chi tiết</a>
            </div>
          </article>
        <?php endforeach; ?>
        <?php if (!$featuredRooms): ?>
          <p>Hiện chưa có phòng nổi bật.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="home-section container">
    <div class="section-heading section-heading-row">
      <div><p class="eyebrow">Không gian nghỉ dưỡng</p><h2>Tất cả phòng</h2></div>
      <a class="text-link" href="<?= e(url('rooms')) ?>">Xem danh sách phòng</a>
    </div>
    <form class="home-room-filter" action="<?= e(url('home')) ?>" method="get">
      <label for="home-room-type">Loại phòng</label>
      <select id="home-room-type" name="room_type_id">
        <option value="">Tất cả loại phòng</option>
        <?php foreach ($roomTypes as $roomType): ?>
          <option value="<?= e($roomType['id']) ?>" <?= $selectedRoomTypeId === (int) $roomType['id'] ? 'selected' : '' ?>><?= e($roomType['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="home-min-price">Giá từ</label>
      <input id="home-min-price" type="number" name="min_price" min="0" max="9999999999.99" step="0.01" value="<?= e($minPrice === null ? '' : $minPrice) ?>" placeholder="Tối thiểu" />
      <label for="home-max-price">Đến</label>
      <input id="home-max-price" type="number" name="max_price" min="0" max="9999999999.99" step="0.01" value="<?= e($maxPrice === null ? '' : $maxPrice) ?>" placeholder="Tối đa" />
      <button class="btn btn-primary" type="submit">Lọc phòng</button>
      <?php if ($hasRoomFilters): ?><a class="text-link" href="<?= e(url('home')) ?>">Xóa bộ lọc</a><?php endif; ?>
    </form>
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
            <p class="room-status room-status-<?= e($room['status']) ?>"><?= $room['status'] === 'available' ? 'Có thể đặt' : ($room['status'] === 'occupied' ? 'Đang sử dụng' : 'Đang bảo trì') ?></p>
            <a class="text-link" href="<?= e(url('room-detail', array('id' => $room['id']))) ?>">Xem chi tiết</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$rooms): ?><p>Không tìm thấy phòng phù hợp với bộ lọc.</p><?php endif; ?>
    </div>
    <p class="room-list-note">Trang chủ hiển thị tối đa 6 phòng. Dùng bộ lọc hoặc xem danh sách để khám phá thêm.</p>
  </section>

  <section class="home-section container" id="amenities">
    <div class="section-heading"><p class="eyebrow">Trải nghiệm trọn vẹn</p><h2>Tiện nghi dành cho bạn</h2></div>
    <div class="amenities-grid">
      <article class="amenity-item"><span class="amenity-number">01</span><h3>Ẩm thực tại chỗ</h3><p>Thưởng thức bữa sáng và các món ăn được chuẩn bị mỗi ngày.</p></article>
      <article class="amenity-item"><span class="amenity-number">02</span><h3>Wi-Fi tốc độ cao</h3><p>Kết nối ổn định trong phòng và các khu vực chung.</p></article>
      <article class="amenity-item"><span class="amenity-number">03</span><h3>Hỗ trợ 24/7</h3><p>Đội ngũ lễ tân luôn sẵn sàng hỗ trợ trong suốt kỳ nghỉ.</p></article>
      <article class="amenity-item"><span class="amenity-number">04</span><h3>Vị trí thuận tiện</h3><p>Dễ dàng di chuyển đến các điểm tham quan và trung tâm thành phố.</p></article>
    </div>
  </section>
</main>
