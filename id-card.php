<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$card = null;
$searched = false;
$searchId = trim($_GET['id_no'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $searchId = trim($_POST['id_no'] ?? '');
}
if ($searchId !== '') {
    $searched = true;
    // FIX: prepared statement - the old search pasted the typed ID straight into SQL
    $stmt = mysqli_prepare($conn, 'SELECT * FROM cards WHERE id_no = ?');
    mysqli_stmt_bind_param($stmt, 's', $searchId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $card = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($stmt);
}

$pageTitle = 'Generate ID Card';
require_once __DIR__ . '/header.php';
?>
<div class="page-head"><h1>Generate ID Card</h1><p>Enter an ID Card No to preview the card, then download it as a PNG or print it</p></div>

<div class="card" style="max-width:640px">
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-group"><label for="id_no">Student / Employee ID Card No</label>
      <input id="id_no" name="id_no" value="<?= e($searchId) ?>" placeholder="Enter ID Card No" required>
      <small style="color:var(--muted)">Every person must have a unique ID number.</small>
    </div>
    <button class="btn" type="submit" name="search">Generate Card</button>
    <a class="btn btn-light" href="add.php">+ Add New Card</a>
  </form>
</div>

<?php if ($searched && $card === null): ?>
  <div class="alert alert-error" style="max-width:640px">No ID card found for ID No <strong><?= e($searchId) ?></strong>. Please check the number, or <a href="add.php">add a new card</a> first.</div>
<?php endif; ?>

<?php if ($card): ?>
  <?php
    $expired = !empty($card['exp_date']) && $card['exp_date'] < date('Y-m-d');
    $photoPath = $card['image'] ?? '';
    $hasPhoto = $photoPath !== '' && is_file(__DIR__ . '/' . $photoPath);
    $fmt = fn($d) => $d ? date('d M Y', strtotime($d)) : '-';
  ?>
  <div id="mycard">
    <div class="id-card">
      <div class="id-card-head">
        <img src="assets/images/logo.jpg" alt="Logo">
        <div class="org">Coding Cush<small>Student / Employee ID Card</small></div>
        <?php if ($expired): ?><span class="badge badge-admin" style="margin-left:auto">Expired</span><?php else: ?><span class="badge badge-active" style="margin-left:auto">Valid</span><?php endif; ?>
      </div>
      <div class="id-card-body">
        <?php if ($hasPhoto): ?><img class="id-photo" src="<?= e($photoPath) ?>" alt="Photo">
        <?php else: ?><div class="id-photo-fallback"><?= e(initials($card['name'])) ?></div><?php endif; ?>
        <div class="id-main">
          <div class="id-name"><?= e($card['name']) ?></div>
          <div class="id-sub"><?= e($card['grade'] ?: 'Student') ?></div>
          <div class="id-grid">
            <div><span class="lbl">ID No</span><div class="val"><?= e($card['id_no']) ?></div></div>
            <div><span class="lbl">Phone</span><div class="val"><?= e($card['phone'] ?? '-') ?></div></div>
            <div><span class="lbl">Date of Birth</span><div class="val"><?= e($fmt($card['dob'] ?? '')) ?></div></div>
            <div><span class="lbl">Email</span><div class="val"><?= e($card['email'] ?? '-') ?></div></div>
            <div><span class="lbl">Joined Date</span><div class="val"><?= e($fmt($card['date'] ?? '')) ?></div></div>
            <div><span class="lbl">Expire Date</span><div class="val <?= $expired ? 'id-expired' : '' ?>"><?= e($fmt($card['exp_date'] ?? '')) ?></div></div>
          </div>
        </div>
      </div>
      <div class="id-card-foot">
        <div class="id-address"><span class="lbl">Address</span><?= e($card['address'] ?: '-') ?></div>
        <div class="id-sign"><div class="line"></div>Your Signature</div>
      </div>
    </div>
  </div>
  <div class="no-print" style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:22px">
    <button class="btn" type="button" onclick="downloadCard()">Download ID Card (PNG)</button>
    <button class="btn btn-amber" type="button" onclick="window.print()">Print</button>
    <a class="btn btn-light" href="edit.php?sno=<?= (int)$card['sno'] ?>">Edit Details</a>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
  <script>
    function downloadCard() {
      var node = document.getElementById('mycard');
      domtoimage.toPng(node)
        .then(function (dataUrl) {
          var link = document.createElement('a');
          link.download = <?= json_encode('id-card-' . preg_replace('/[^A-Za-z0-9\-]/', '', (string)$card['id_no']) . '.png') ?>;
          link.href = dataUrl;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch(function (error) { console.error('Download failed', error); alert('Sorry, the download failed. You can use the Print button instead.'); });
    }
  </script>
<?php endif; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
