<?php
// Shared helpers: session, escaping, CSRF, flash messages, photo upload, validation.
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    if (!isset($_POST['csrf_token']) || !hash_equals(csrf_token(), (string)$_POST['csrf_token'])) {
        set_flash('error', 'Invalid security token. Please try again.');
        redirect('index.php');
    }
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
}

function flash(string $type): ?string {
    if (!empty($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

function initials(string $name): string {
    $out = '';
    foreach (array_slice(preg_split('/\s+/', trim($name)), 0, 2) as $part) {
        $out .= strtoupper(mb_substr($part, 0, 1));
    }
    return $out ?: 'I';
}

const GRADES = ['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th','11th','12th','BCA','BBA','MCA','MBA','B.Tech','M.Tech','Teacher','Staff','Employee','Other'];

/**
 * Secure photo upload.
 * FIX (old version): ANY file type could be uploaded with its original name into
 * assets/uploads/ (a PHP shell upload = server takeover), and when the upload
 * failed the record was still inserted with the broken path.
 * Now: JPG/PNG/WEBP only, 2 MB max, random file name. Returns the path to store
 * in the database (same 'assets/uploads/...' format as existing records), or an error.
 * Returns [path|null, error|null]. On edit, no new file keeps the old path.
 */
function handle_photo_upload(string $inputName = 'image', ?string $oldPath = null): array {
    if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [$oldPath, null];
    }
    $file = $_FILES[$inputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Photo upload failed. Please try again.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'Photo must be under 2 MB.'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return [null, 'Photo must be a JPG, PNG or WEBP image.'];
    }
    $dir = __DIR__ . '/../assets/uploads/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = 'photo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        return [null, 'Could not save the photo.'];
    }
    return ['assets/uploads/' . $name, null];
}

/**
 * Validate the card form (add + edit). Field names match the original form.
 * Returns [data, errors] where data maps to the `cards` table columns.
 */
function validate_card(array $post): array {
    $data = [
        'name'     => trim($post['name'] ?? ''),
        'grade'    => trim($post['grade'] ?? ''),
        'id_no'    => trim($post['id_no'] ?? ''),
        'email'    => trim($post['email'] ?? ''),
        'phone'    => trim($post['phone'] ?? ''),
        'address'  => trim($post['address'] ?? ''),
        'dob'      => trim($post['dob'] ?? ''),
        'exp_date' => trim($post['exp_date'] ?? ''),
    ];
    $errors = [];
    if (strlen($data['name']) < 2) $errors['name'] = 'Name is required.';
    if ($data['id_no'] === '') $errors['id_no'] = 'ID Card No is required.';
    if (strlen($data['id_no']) > 15) $errors['id_no'] = 'ID Card No must be 15 characters or less.';
    if ($data['grade'] !== '' && !in_array($data['grade'], GRADES, true)) $errors['grade'] = 'Please choose a valid class / role.';
    if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if ($data['phone'] === '' || !preg_match('/^[0-9+\-\s]{7,15}$/', $data['phone'])) $errors['phone'] = 'Enter a valid phone number.';
    foreach (['dob' => 'Date of birth', 'exp_date' => 'Expiry date'] as $key => $label) {
        if ($data[$key] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data[$key])) {
            $errors[$key] = $label . ' must be a valid date.';
        }
    }
    if (strlen($data['address']) > 100) $errors['address'] = 'Address must be 100 characters or less.';
    return [$data, $errors];
}

function id_no_taken(mysqli $conn, string $idNo, int $excludeSno = 0): bool {
    $stmt = mysqli_prepare($conn, 'SELECT sno FROM cards WHERE id_no = ? AND sno <> ?');
    mysqli_stmt_bind_param($stmt, 'si', $idNo, $excludeSno);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $taken = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $taken;
}
