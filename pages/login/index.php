<?php
$pageTitle = 'Đăng nhập | HotelBooking';
?>
<main class="container page-content account-page">
  <section class="form-box account-card">
    <p class="eyebrow">Chào mừng trở lại</p>
    <h1>Đăng nhập</h1>
    <?php if ($notice = take_flash('notice')): ?><p class="form-success" role="status"><?= e($notice) ?></p><?php endif; ?>
    <?php if ($message = take_flash('success')): ?><p class="form-success" role="status"><?= e($message) ?></p><?php endif; ?>
    <?php foreach ($authErrors as $error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
    <form action="<?= e(url('login')) ?>" method="post">
      <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
      <label for="login-email">Email</label>
      <input id="login-email" type="email" name="email" autocomplete="email" maxlength="190" value="<?= e($loginEmail) ?>" required />
      <label for="login-password">Mật khẩu</label>
      <input id="login-password" type="password" name="password" autocomplete="current-password" required />
      <button class="btn btn-primary" type="submit">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="<?= e(url('register')) ?>">Đăng ký ngay</a></p>
  </section>
</main>
