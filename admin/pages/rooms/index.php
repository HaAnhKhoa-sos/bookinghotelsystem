<?php
$pageTitle = 'Quản lý phòng | HotelBooking Admin';
$roomTypes = getRoomTypes();
$filters = array(
    'q' => isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '',
    'type_id' => isset($_GET['type_id']) && is_string($_GET['type_id']) && ctype_digit($_GET['type_id']) ? $_GET['type_id'] : '',
    'status' => isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '',
    'min_price' => validPriceFilter(isset($_GET['min_price']) && is_string($_GET['min_price']) ? $_GET['min_price'] : ''),
    'max_price' => validPriceFilter(isset($_GET['max_price']) && is_string($_GET['max_price']) ? $_GET['max_price'] : ''),
);
$validStatuses = array('available', 'occupied', 'maintenance');
if (!in_array($filters['status'], $validStatuses, true)) {
    $filters['status'] = '';
}
$rooms = getAdminRooms($filters);
$statusLabels = array('available' => 'Trống', 'occupied' => 'Đang sử dụng', 'maintenance' => 'Bảo trì');
$statusClasses = array('available' => 'status-confirmed', 'occupied' => 'status-booked', 'maintenance' => 'status-maintenance');
?>
<main class="admin-main">
  <header class="admin-page-heading">
    <div><p class="eyebrow">Danh mục lưu trú</p><h1>Quản lý phòng</h1></div>
    <a class="btn btn-primary" href="<?= e(admin_url('rooms', array('action' => 'create'))) ?>">+ Thêm phòng</a>
  </header>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'created'): ?>
    <p class="admin-success" role="status">Đã thêm phòng thành công.</p>
  <?php endif; ?>
  <section class="admin-card">
    <form class="admin-toolbar admin-filter" action="<?= e(base_url('admin/index.php')) ?>" method="get">
      <input type="hidden" name="page" value="rooms" />
      <label class="search-label">Tìm phòng<input class="search-box" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Tên hoặc số phòng" /></label>
      <label>Loại phòng<select name="type_id"><option value="">Tất cả loại</option>
        <?php foreach ($roomTypes as $roomType): ?>
          <option value="<?= e($roomType['id']) ?>" <?= $filters['type_id'] == $roomType['id'] ? 'selected' : '' ?>><?= e($roomType['name']) ?></option>
        <?php endforeach; ?>
      </select></label>
      <label>Trạng thái<select name="status"><option value="">Tất cả trạng thái</option>
        <?php foreach ($statusLabels as $status => $label): ?>
          <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select></label>
      <label>Giá từ<input type="number" name="min_price" min="0" max="9999999999.99" step="0.01" value="<?= e($filters['min_price'] === null ? '' : $filters['min_price']) ?>" placeholder="Tối thiểu" /></label>
      <label>Đến<input type="number" name="max_price" min="0" max="9999999999.99" step="0.01" value="<?= e($filters['max_price'] === null ? '' : $filters['max_price']) ?>" placeholder="Tối đa" /></label>
      <button class="btn btn-primary" type="submit">Lọc</button>
    </form>
    <div class="table-scroll"><table class="admin-table">
      <thead><tr><th>Số phòng</th><th>Tên phòng</th><th>Loại</th><th>Sức chứa</th><th>Giá / đêm</th><th>Trạng thái</th></tr></thead>
      <tbody>
        <?php foreach ($rooms as $room): ?>
          <tr>
            <td><?= e($room['room_number']) ?></td>
            <td><?= e($room['room_name']) ?></td>
            <td><?= e($room['room_type_name']) ?></td>
            <td><?= e($room['capacity']) ?> khách</td>
            <td><?= e(money($room['price'])) ?></td>
            <td><span class="admin-status <?= e($statusClasses[$room['status']]) ?>"><?= e($statusLabels[$room['status']]) ?></span></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rooms): ?><tr><td colspan="6">Không tìm thấy phòng phù hợp.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
    <p class="table-caption">Hiển thị tối đa 100 phòng.</p>
  </section>

  <?php if ($showRoomForm): ?>
    <section class="admin-card admin-form-card">
      <h2>Thêm phòng</h2>
      <?php foreach ($errors as $error): ?><p class="admin-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
      <form action="<?= e(admin_url('rooms')) ?>" method="post">
        <div class="admin-form-grid">
          <div class="form-group"><label for="room-number">Số phòng *</label><input class="form-control" type="text" id="room-number" name="room_number" maxlength="20" value="<?= e($formValues['room_number']) ?>" required /></div>
          <div class="form-group"><label for="room-name">Tên phòng *</label><input class="form-control" type="text" id="room-name" name="room_name" maxlength="100" value="<?= e($formValues['room_name']) ?>" required /></div>
          <div class="form-group"><label for="room-type">Loại phòng *</label><select class="form-control" id="room-type" name="room_type_id" required><option value="">Chọn loại phòng</option>
            <?php foreach ($roomTypes as $roomType): ?>
              <option value="<?= e($roomType['id']) ?>" <?= $formValues['room_type_id'] == $roomType['id'] ? 'selected' : '' ?>><?= e($roomType['name']) ?> · <?= e($roomType['capacity']) ?> khách</option>
            <?php endforeach; ?>
          </select></div>
          <div class="form-group"><label for="room-price">Giá phòng / đêm *</label><input class="form-control" type="number" id="room-price" name="price" min="0" max="9999999999.99" step="0.01" value="<?= e($formValues['price']) ?>" required /></div>
          <div class="form-group"><label for="room-status">Trạng thái *</label><select class="form-control" id="room-status" name="status" required>
            <?php foreach ($statusLabels as $status => $label): ?><option value="<?= e($status) ?>" <?= $formValues['status'] === $status ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
          </select></div>
          <div class="form-group"><label for="bed">Loại giường</label><input class="form-control" type="text" id="bed" name="bed_description" maxlength="100" value="<?= e($formValues['bed_description']) ?>" /></div>
          <div class="form-group"><label for="image">Đường dẫn hình ảnh</label><input class="form-control" type="url" id="image" name="image_url" value="<?= e($formValues['image_url']) ?>" /></div>
          <fieldset class="admin-checkboxes"><legend>Hiển thị</legend><label><input type="checkbox" name="is_featured" value="1" <?= $formValues['is_featured'] === '1' ? 'checked' : '' ?> /> Phòng nổi bật</label></fieldset>
        </div>
        <div class="form-actions"><a class="btn btn-secondary admin-cancel" href="<?= e(admin_url('rooms')) ?>">Hủy</a><button class="btn btn-primary" type="submit">Lưu phòng</button></div>
      </form>
    </section>
  <?php endif; ?>
</main>
