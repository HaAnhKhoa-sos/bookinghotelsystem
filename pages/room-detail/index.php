<?php if (!$room): ?>
  <main>
    <section class="container page-content">
      <h1>Không tìm thấy phòng</h1>
      <p>Phòng bạn đang tìm không tồn tại hoặc liên kết không hợp lệ.</p>
      <a class="btn btn-primary" href="<?= e(url('rooms')) ?>">Quay lại danh sách phòng</a>
    </section>
  </main>
<?php else: ?>
  <main>
    <section class="container page-content room-detail-page">
      <a class="back-link" href="<?= e(url('rooms')) ?>">← Quay lại danh sách phòng</a>
      <div class="detail-layout">
        <div class="detail-photo"><img src="<?= e($room['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1400&q=90') ?>" alt="<?= e($room['room_name']) ?>" /></div>
        <div class="detail-summary">
          <p class="eyebrow"><?= e($room['room_type_name']) ?> · Phòng <?= e($room['room_number']) ?></p>
          <h1><?= e($room['room_name']) ?></h1>
          <p class="detail-price"><?= e(money($room['price'])) ?> <span>/ đêm</span></p>
          <p class="detail-lead"><?= e($room['description']) ?></p>
          <dl class="room-specs">
            <div><dt>Loại phòng</dt><dd><?= e($room['room_type_name']) ?></dd></div>
            <div><dt>Diện tích</dt><dd><?= e($room['area_sqm']) ?> m²</dd></div>
            <div><dt>Sức chứa</dt><dd><?= e($room['capacity']) ?> người</dd></div>
            <div><dt>Giường</dt><dd><?= e($room['bed_description'] ?: 'Chưa cập nhật') ?></dd></div>
          </dl>
          <p class="room-status room-status-<?= e($room['status']) ?>"><?= $room['status'] === 'available' ? 'Có thể đặt' : ($room['status'] === 'occupied' ? 'Đang sử dụng' : 'Đang bảo trì') ?></p>
          <a class="btn btn-primary booking-cta" href="<?= e(url('rooms', array('room_type_id' => $room['room_type_id']))) ?>">Xem thêm phòng cùng loại</a>
        </div>
      </div>
      <div class="detail-information">
        <section><h2>Tiện nghi phòng</h2><ul class="amenity-list"><li>Wi-Fi tốc độ cao</li><li>Điều hòa</li><li>TV màn hình phẳng</li><li>Phòng tắm riêng</li><li>Dọn phòng hằng ngày</li></ul></section>
        <section><h2>Mô tả</h2><p><?= e($room['description']) ?></p></section>
      </div>
    </section>
  </main>
<?php endif; ?>
