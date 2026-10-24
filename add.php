<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$old = [];
$currentImage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$data, $errors] = validate_card($_POST);
    if (!$errors && id_no_taken($conn, $data['id_no'])) {
        $errors['id_no'] = 'This ID Card No is already added. Please use a different one.';
    }
    [$image, $imageError] = handle_photo_upload('image');
    if ($imageError) {
        $errors['image'] = $imageError;
    } elseif ($image === null) {
        $errors['image'] = 'Please choose a photo for the ID card.';
    }
    if (!$errors) {
        // FIXES vs old version: prepared statement (was SQL injection), the Grade is
        // actually saved now (the old INSERT silently dropped it), and the email is
        // stored exactly as typed (the old code saved every email with a stray ']'
        // appended - see the old database dump: 'rishi@gmail.com]').
        $stmt = mysqli_prepare($conn, 'INSERT INTO cards (name, grade, id_no, email, phone, address, dob, exp_date, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssssssss',
            $data['name'], $data['grade'], $data['id_no'], $data['email'], $data['phone'],
            $data['address'], $data['dob'], $data['exp_date'], $image);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', 'ID card added successfully. You can generate it now.');
            redirect('id-card.php?id_no=' . urlencode($data['id_no']));
        }
        mysqli_stmt_close($stmt);
        $errors['form'] = 'The record was not inserted. Please try again.';
    }
    $old = $_POST;
}

$pageTitle = 'Add New Card';
require_once __DIR__ . '/header.php';
?>
<div class="page-head"><h1>Add New Card</h1><p>Enter the person's details and photo</p></div>
<?php if (isset($errors['form'])): ?><div class="alert alert-error"><?= e($errors['form']) ?></div><?php endif; ?>
<?php $formTitle = 'New ID Card Details'; require __DIR__ . '/includes/card-form.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
