<?php
$pageTitle = 'Quản lý khách hàng | HotelBooking Admin';
$search = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$customers = getAdminCustomers(array('q' => $search));
?>
<main class="admin-main">
  <header class="admin-page-heading">
    <div><p class="eyebrow">Hồ sơ lưu trú</p><h1>Quản lý khách hàng</h1></div>
    <a class="btn btn-primary" href="<?= e(admin_url('customers', array('action' => 'create'))) ?>">+ Thêm khách hàng</a>
  </header>
  <?php if (!empty($adminActionError)): ?><p class="admin-error" role="alert"><?= e($adminActionError) ?></p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'created'): ?>
    <p class="admin-success" role="status">Đã thêm khách hàng thành công.</p>
  <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'updated'): ?>
    <p class="admin-success" role="status">Đã cập nhật thông tin khách hàng.</p>
  <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'deleted'): ?>
    <p class="admin-success" role="status">Đã xóa khách hàng thành công.</p>
  <?php endif; ?>

  <section class="admin-card">
    <form class="admin-toolbar admin-filter admin-filter-compact" action="<?= e(base_url('admin/index.php')) ?>" method="get">
      <input type="hidden" name="page" value="customers" />
      <label class="search-label">Tìm khách hàng<input class="search-box" type="search" name="q" value="<?= e($search) ?>" placeholder="Họ tên, email hoặc số điện thoại" /></label>
      <button class="btn btn-primary" type="submit">Tìm kiếm</button>
    </form>
    <div class="table-scroll"><table class="admin-table">
      <thead><tr><th>Khách hàng</th><th>Email</th><th>Điện thoại</th><th>Số lượt đặt</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach ($customers as $customer): ?>
          <tr>
            <td><?= e($customer['full_name']) ?></td>
            <td><?= e($customer['email']) ?></td>
            <td><?= e($customer['phone']) ?></td>
            <td><?= e($customer['booking_count']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($customer['created_at']))) ?></td>
            <td><div class="action-btns">
              <a class="btn-sm btn-view" href="<?= e(admin_url('customers', array('action' => 'edit', 'id' => $customer['id']))) ?>">Sửa</a>
              <form action="<?= e(admin_url('customers', array('action' => 'delete', 'id' => $customer['id']))) ?>" method="post" onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                <input type="hidden" name="_csrf_token" value="<?= e(admin_csrf_token()) ?>" />
                <button class="btn-sm btn-delete" type="submit">Xóa</button>
              </form>
            </div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$customers): ?><tr><td colspan="6">Không tìm thấy khách hàng phù hợp.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
    <p class="table-caption">Hiển thị tối đa 100 khách hàng.</p>
  </section>

  <?php if ($showCustomerForm): ?>
    <section class="admin-card admin-form-card">
      <h2><?= e($formTitle) ?></h2>
      <?php foreach ($errors as $error): ?><p class="admin-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
      <form action="<?= e(admin_url('customers', array('action' => $requestedAction, 'id' => $requestedAction === 'edit' ? $customerId : null))) ?>" method="post">
        <input type="hidden" name="_csrf_token" value="<?= e(admin_csrf_token()) ?>" />
        <div class="admin-form-grid">
          <div class="form-group"><label for="customer-name">Họ tên *</label><input class="form-control" type="text" id="customer-name" name="full_name" maxlength="150" value="<?= e($formValues['full_name']) ?>" required /></div>
          <div class="form-group"><label for="customer-email">Email *</label><input class="form-control" type="email" id="customer-email" name="email" maxlength="190" value="<?= e($formValues['email']) ?>" required /></div>
          <div class="form-group"><label for="customer-phone">Số điện thoại *</label><input class="form-control" type="tel" id="customer-phone" name="phone" maxlength="30" value="<?= e($formValues['phone']) ?>" required /></div>
        </div>
        <div class="form-actions"><a class="btn btn-secondary admin-cancel" href="<?= e(admin_url('customers')) ?>">Hủy</a><button class="btn btn-primary" type="submit"><?= $requestedAction === 'edit' ? 'Cập nhật khách hàng' : 'Lưu khách hàng' ?></button></div>
      </form>
    </section>
  <?php endif; ?>
</main>
