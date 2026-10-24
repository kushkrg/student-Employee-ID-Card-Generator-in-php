<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// FIX: the old version deleted via a GET link (index.php?delete=123) with the ID
// pasted straight into SQL - no CSRF token, no confirmation on the server, and
// wide open to SQL injection and accidental/prefetch deletes.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
verify_csrf();

$sno = (int)($_POST['sno'] ?? 0);

$stmt = mysqli_prepare($conn, 'SELECT image FROM cards WHERE sno = ?');
mysqli_stmt_bind_param($stmt, 'i', $sno);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $image);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, 'DELETE FROM cards WHERE sno = ?');
mysqli_stmt_bind_param($stmt, 'i', $sno);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Remove the photo file too - but only if it really lives inside assets/uploads/
if (!empty($image)) {
    $uploadsDir = realpath(__DIR__ . '/assets/uploads');
    $filePath = realpath(__DIR__ . '/' . $image);
    if ($uploadsDir && $filePath && strpos($filePath, $uploadsDir) === 0 && is_file($filePath)) {
        unlink($filePath);
    }
}
set_flash('success', 'ID card deleted successfully.');
redirect('index.php');
