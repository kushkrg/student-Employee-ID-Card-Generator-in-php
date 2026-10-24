<?php
// Shared Add / Edit card form. Expects: $old, $errors, $formTitle, $currentImage
$v = fn(string $k) => e($old[$k] ?? '');
$err = function (string $k) use ($errors) {
    return isset($errors[$k]) ? '<div class="error-text">' . e($errors[$k]) . '</div>' : '';
};
?>
<div class="card">
  <h2><?= e($formTitle) ?></h2>
  <form method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="form-section">Person Details</div>
      <div class="form-group"><label>Full Name *</label><input name="name" value="<?= $v('name') ?>" placeholder="e.g. Aarav Sharma"><?= $err('name') ?></div>
      <div class="form-group"><label>Class / Grade / Role</label><select name="grade"><option value="">-- Select --</option><?php foreach (GRADES as $g): ?><option value="<?= e($g) ?>" <?= ($old['grade'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select><?= $err('grade') ?></div>
      <div class="form-group"><label>ID Card No *</label><input name="id_no" maxlength="15" value="<?= $v('id_no') ?>" placeholder="e.g. 2026001"><?= $err('id_no') ?></div>
      <div class="form-group"><label>Date of Birth</label><input name="dob" type="date" value="<?= $v('dob') ?>"><?= $err('dob') ?></div>
      <div class="form-section">Contact Details</div>
      <div class="form-group"><label>Email *</label><input name="email" type="email" value="<?= $v('email') ?>" placeholder="person@example.com"><?= $err('email') ?></div>
      <div class="form-group"><label>Phone No *</label><input name="phone" value="<?= $v('phone') ?>" placeholder="10-digit phone number"><?= $err('phone') ?></div>
      <div class="form-group"><label>Address</label><input name="address" maxlength="100" value="<?= $v('address') ?>" placeholder="City, State"><?= $err('address') ?></div>
      <div class="form-group"><label>Card Expiry Date</label><input name="exp_date" type="date" value="<?= $v('exp_date') ?>"><?= $err('exp_date') ?></div>
      <div class="form-section">Photo</div>
      <div class="form-group"><label>Photo (JPG/PNG/WEBP, max 2MB)<?= empty($currentImage) ? ' *' : '' ?></label><input name="image" type="file" accept=".jpg,.jpeg,.png,.webp"><?= $err('image') ?>
      <?php if (!empty($currentImage)): ?><small style="color:var(--muted)">Current photo is saved - choose a file only to replace it.</small><?php endif; ?></div>
    </div>
    <div style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
      <button class="btn" type="submit">Save Card</button>
      <a class="btn btn-light" href="index.php">Cancel</a>
    </div>
  </form>
</div>
