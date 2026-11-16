<?php
$pageTitle = 'Đặt phòng | HotelBooking';
?>
<main class="container page-content account-page">
  <section class="form-box account-card">
    <p class="eyebrow">Đặt kỳ nghỉ của bạn</p>
    <h1>Thông tin đặt phòng</h1>
    <?php foreach ($bookingErrors as $error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
    <?php if ($bookingRoom): ?>
      <div class="booking-summary">
        <h2><?= e($bookingRoom['room_name']) ?> · Phòng <?= e($bookingRoom['room_number']) ?></h2>
        <p><?= e($bookingRoom['room_type_name']) ?> · Tối đa <?= e($bookingRoom['capacity']) ?> khách</p>
        <p class="price"><?= e(money($bookingRoom['price'])) ?> / đêm</p>
      </div>
      <?php if ($bookingRoom['status'] === 'available' && current_customer_id()): ?>
        <form action="<?= e(url('booking', array('room_id' => $roomId))) ?>" method="post">
          <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
          <label for="booking-check-in">Ngày nhận phòng</label>
          <input id="booking-check-in" type="date" name="check_in" min="<?= e(date('Y-m-d')) ?>" value="<?= e($bookingValues['check_in']) ?>" required />
          <label for="booking-check-out">Ngày trả phòng</label>
          <input id="booking-check-out" type="date" name="check_out" min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" value="<?= e($bookingValues['check_out']) ?>" required />
          <label for="booking-guests">Số khách</label>
          <input id="booking-guests" type="number" name="guest_count" min="1" max="<?= e($bookingRoom['capacity']) ?>" value="<?= e($bookingValues['guest_count']) ?>" required />
          <p>Đơn đặt phòng sẽ ở trạng thái chờ xác nhận. Tổng tiền được tính theo giá phòng hiện tại và số đêm lưu trú.</p>
          <button class="btn btn-primary" type="submit">Gửi yêu cầu đặt phòng</button>
        </form>
      <?php elseif ($bookingRoom['status'] !== 'available'): ?>
        <p>Phòng này hiện không thể đặt.</p>
      <?php endif; ?>
    <?php endif; ?>
    <p><a href="<?= e(url('rooms')) ?>">Quay lại danh sách phòng</a></p>
  </section>
</main>
