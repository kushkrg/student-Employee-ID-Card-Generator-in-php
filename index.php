<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$totalCards = (int)mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM cards'))[0];
$expiredCards = (int)mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM cards WHERE exp_date <> '' AND exp_date < CURDATE()"))[0];

$where = '';
if ($q !== '') {
    $where = ' WHERE name LIKE ? OR id_no LIKE ? OR email LIKE ? OR phone LIKE ?';
}
$countStmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM cards' . $where);
if ($q !== '') { $like = '%' . $q . '%'; mysqli_stmt_bind_param($countStmt, 'ssss', $like, $like, $like, $like); }
mysqli_stmt_execute($countStmt);
mysqli_stmt_bind_result($countStmt, $filteredTotal);
mysqli_stmt_fetch($countStmt);
mysqli_stmt_close($countStmt);

$totalPages = max(1, (int)ceil($filteredTotal / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = mysqli_prepare($conn, 'SELECT * FROM cards' . $where . " ORDER BY sno DESC LIMIT $perPage OFFSET $offset");
if ($q !== '') { $like = '%' . $q . '%'; mysqli_stmt_bind_param($stmt, 'ssss', $like, $like, $like, $like); }
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cards = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$pageTitle = 'All ID Cards';
require_once __DIR__ . '/header.php';
?>
<div class="page-head"><h1>ID Card Generator</h1><p>Student / Employee ID cards - add a person, then generate a printable card</p></div>
<?php if ($s = flash('success')): ?><div class="alert alert-success"><?= e($s) ?></div><?php endif; ?>
<?php if ($f = flash('error')): ?><div class="alert alert-error"><?= e($f) ?></div><?php endif; ?>

<div class="stats">
  <div class="stat-card"><small>Total Cards</small><div class="num"><?= $totalCards ?></div></div>
  <div class="stat-card"><small>Expired Cards</small><div class="num"><?= $expiredCards ?></div></div>
</div>

<div class="filter-card">
  <form class="search-form" method="get">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name, ID card no, email or phone...">
    <button class="btn" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn btn-light" href="index.php">Reset</a><?php endif; ?>
    <a class="btn" href="add.php">+ Add New Card</a>
    <a class="btn btn-amber" href="id-card.php">Generate ID Card</a>
  </form>
</div>

<div class="card">
  <div class="table-wrap"><table>
    <tr><th>Person</th><th>ID Card No</th><th>Class / Role</th><th>Phone</th><th>Expiry</th><th>Actions</th></tr>
    <?php if (!$cards): ?><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:28px">No cards found. <a href="add.php">Add the first card</a>.</td></tr><?php endif; ?>
    <?php foreach ($cards as $row): $expired = !empty($row['exp_date']) && $row['exp_date'] < date('Y-m-d'); ?>
    <tr>
      <td><div class="student-cell">
        <?php if (!empty($row['image']) && is_file(__DIR__ . '/' . $row['image'])): ?><img class="student-photo" src="<?= e($row['image']) ?>" alt="">
        <?php else: ?><span class="student-avatar"><?= e(initials($row['name'])) ?></span><?php endif; ?>
        <span><strong><?= e($row['name']) ?></strong><br><small style="color:var(--muted)"><?= e($row['email'] ?? '') ?></small></span>
      </div></td>
      <td><?= e($row['id_no']) ?></td>
      <td><?= e($row['grade'] ?? '-') ?></td>
      <td><?= e($row['phone'] ?? '-') ?></td>
      <td><?php if (!empty($row['exp_date'])): ?><span class="<?= $expired ? 'id-expired' : '' ?>" style="font-weight:700"><?= e(date('d M Y', strtotime($row['exp_date']))) ?><?= $expired ? ' (Expired)' : '' ?></span><?php else: ?>-<?php endif; ?></td>
      <td><div class="actions">
        <a class="btn btn-sm btn-view" href="id-card.php?id_no=<?= urlencode($row['id_no']) ?>">Generate</a>
        <a class="btn btn-sm btn-edit" href="edit.php?sno=<?= (int)$row['sno'] ?>">Edit</a>
        <form method="post" action="delete.php" data-confirm="Delete this ID card permanently?">
          <?= csrf_field() ?>
          <input type="hidden" name="sno" value="<?= (int)$row['sno'] ?>">
          <button class="btn btn-sm btn-danger" type="submit">Delete</button>
        </form>
      </div></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php if ($i === $page): ?><span class="current"><?= $i ?></span><?php else: ?><a href="?page=<?= $i ?><?= $q !== '' ? '&q=' . urlencode($q) : '' ?>"><?= $i ?></a><?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <p style="margin-top:14px;color:var(--muted);font-size:14px">Showing <?= count($cards) ?> of <?= (int)$filteredTotal ?> card(s)</p>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
