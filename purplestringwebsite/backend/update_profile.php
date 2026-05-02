<?php
session_start();
include_once __DIR__ . '/connection.php';

$con = get_db_connection();
if (!$con) {
    http_response_code(500);
    echo 'DB connection failed';
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/pages/profileEditMode.php');
    exit;
}

$display_name = trim($_POST['display_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

// handle avatar upload
$avatar_filename = null;
if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $f = $_FILES['avatar'];
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $target_dir = __DIR__ . '/../frontend/public/images/avatars/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $target_dir . $newName;
        if (move_uploaded_file($f['tmp_name'], $dest)) {
            $avatar_filename = $newName;
        }
    }
}

// build update query
$parts = [];
$params = [];
$types = '';
if ($display_name !== '') { $parts[] = 'display_name = ?'; $params[] = $display_name; $types .= 's'; }
if ($username !== '') { $parts[] = 'user_name = ?'; $params[] = $username; $types .= 's'; }
if ($bio !== '') { $parts[] = 'bio = ?'; $params[] = $bio; $types .= 's'; }
if ($phone !== '') { $parts[] = 'phone = ?'; $params[] = $phone; $types .= 's'; }
if ($address !== '') { $parts[] = 'address = ?'; $params[] = $address; $types .= 's'; }
if ($avatar_filename !== null) { $parts[] = 'avatar = ?'; $params[] = $avatar_filename; $types .= 's'; }

if (!empty($parts)) {
    $sql = "UPDATE users SET " . implode(', ', $parts) . " WHERE user_id = ?";
    $params[] = $user_id; $types .= 's';
    $stmt = mysqli_prepare($con, $sql);
    // bind params dynamically
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// redirect back to profile page
header('Location: ../frontend/pages/profile.php');
exit;

?>
