<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$sno = (int)($_GET['sno'] ?? 0);
$stmt = mysqli_prepare($conn, 'SELECT * FROM cards WHERE sno = ?');
mysqli_stmt_bind_param($stmt, 'i', $sno);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$card = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$card) {
    set_flash('error', 'ID card not found.');
    redirect('index.php');
}

// FIX: the old Edit could only change name + ID number (in a small modal).
// This page edits every field, including the photo.
$old = [
    'name' => $card['name'], 'grade' => $card['grade'] ?? '', 'id_no' => $card['id_no'],
    'email' => $card['email'] ?? '', 'phone' => $card['phone'] ?? '', 'address' => $card['address'] ?? '',
    'dob' => $card['dob'] ?? '', 'exp_date' => $card['exp_date'] ?? '',
];
$currentImage = $card['image'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$data, $errors] = validate_card($_POST);
    if (!$errors && id_no_taken($conn, $data['id_no'], $sno)) {
        $errors['id_no'] = 'This ID Card No is already used by another card.';
    }
    [$image, $imageError] = handle_photo_upload('image', $card['image']);
    if ($imageError) $errors['image'] = $imageError;
    if (!$errors) {
        $stmt = mysqli_prepare($conn, 'UPDATE cards SET name=?, grade=?, id_no=?, email=?, phone=?, address=?, dob=?, exp_date=?, image=? WHERE sno=?');
        mysqli_stmt_bind_param($stmt, 'sssssssssi',
            $data['name'], $data['grade'], $data['id_no'], $data['email'], $data['phone'],
            $data['address'], $data['dob'], $data['exp_date'], $image, $sno);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', 'ID card updated successfully.');
            redirect('id-card.php?id_no=' . urlencode($data['id_no']));
        }
        mysqli_stmt_close($stmt);
        $errors['form'] = 'The record was not updated. Please try again.';
    }
    $old = $_POST;
    $currentImage = $image ?? $currentImage;
}

$pageTitle = 'Edit Card';
require_once __DIR__ . '/header.php';
?>
<div class="page-head"><h1>Edit ID Card</h1><p><?= e($card['name']) ?> &bull; <?= e($card['id_no']) ?></p></div>
<?php if (isset($errors['form'])): ?><div class="alert alert-error"><?= e($errors['form']) ?></div><?php endif; ?>
<?php $formTitle = 'Edit ID Card Details'; require __DIR__ . '/includes/card-form.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
