<?php
$pageTitle = 'Quản lý loại phòng | HotelBooking Admin';
$roomTypes = getAdminRoomTypes();
?>
<main class="admin-main">
  <header class="admin-page-heading">
    <div><p class="eyebrow">Danh mục lưu trú</p><h1>Quản lý loại phòng</h1></div>
    <a class="btn btn-primary" href="<?= e(admin_url('room-types', array('action' => 'create'))) ?>">+ Thêm loại phòng</a>
  </header>
  <?php if (!empty($adminActionError)): ?><p class="admin-error" role="alert"><?= e($adminActionError) ?></p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'created'): ?><p class="admin-success" role="status">Đã thêm loại phòng.</p>
  <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'updated'): ?><p class="admin-success" role="status">Đã cập nhật loại phòng.</p>
  <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'deleted'): ?><p class="admin-success" role="status">Đã xóa loại phòng.</p><?php endif; ?>
  <section class="admin-card">
    <div class="table-scroll"><table class="admin-table">
      <thead><tr><th>Tên loại</th><th>Sức chứa</th><th>Diện tích</th><th>Số phòng</th><th>Mô tả</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach ($roomTypes as $roomType): ?>
          <tr>
            <td><?= e($roomType['name']) ?></td>
            <td><?= e($roomType['capacity']) ?> khách</td>
            <td><?= e($roomType['area_sqm']) ?> m²</td>
            <td><?= e($roomType['room_count']) ?></td>
            <td><?= e($roomType['description']) ?></td>
            <td><div class="action-btns">
              <a class="btn-sm btn-view" href="<?= e(admin_url('room-types', array('action' => 'edit', 'id' => $roomType['id']))) ?>">Sửa</a>
              <form action="<?= e(admin_url('room-types', array('action' => 'delete', 'id' => $roomType['id']))) ?>" method="post" onsubmit="return confirm('Bạn có chắc muốn xóa loại phòng này?')">
                <input type="hidden" name="_csrf_token" value="<?= e(admin_csrf_token()) ?>" />
                <button class="btn-sm btn-delete" type="submit" <?= (int) $roomType['room_count'] > 0 ? 'disabled title="Đang được sử dụng bởi phòng"' : '' ?>>Xóa</button>
              </form>
            </div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$roomTypes): ?><tr><td colspan="6">Chưa có loại phòng.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </section>
  <?php if ($showRoomTypeForm): ?>
    <section class="admin-card admin-form-card">
      <h2><?= e($roomTypeFormTitle) ?></h2>
      <?php foreach ($roomTypeErrors as $error): ?><p class="admin-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
      <form action="<?= e(admin_url('room-types', array('action' => $requestedAction, 'id' => $requestedAction === 'edit' ? $roomTypeId : null))) ?>" method="post">
        <input type="hidden" name="_csrf_token" value="<?= e(admin_csrf_token()) ?>" />
        <div class="admin-form-grid">
          <div class="form-group"><label for="type-name">Tên loại phòng *</label><input class="form-control" id="type-name" name="name" maxlength="100" value="<?= e($roomTypeValues['name']) ?>" required /></div>
          <div class="form-group"><label for="type-capacity">Sức chứa (khách) *</label><input class="form-control" id="type-capacity" type="number" name="capacity" min="1" max="255" value="<?= e($roomTypeValues['capacity']) ?>" required /></div>
          <div class="form-group"><label for="type-area">Diện tích (m²) *</label><input class="form-control" id="type-area" type="number" name="area_sqm" min="0.01" max="9999.99" step="0.01" value="<?= e($roomTypeValues['area_sqm']) ?>" required /></div>
          <div class="form-group form-group-wide"><label for="type-description">Mô tả *</label><textarea class="form-control" id="type-description" name="description" rows="4" maxlength="5000" required><?= e($roomTypeValues['description']) ?></textarea></div>
        </div>
        <div class="form-actions"><a class="btn btn-secondary admin-cancel" href="<?= e(admin_url('room-types')) ?>">Hủy</a><button class="btn btn-primary" type="submit"><?= $requestedAction === 'edit' ? 'Cập nhật loại phòng' : 'Lưu loại phòng' ?></button></div>
      </form>
    </section>
  <?php endif; ?>
</main>
