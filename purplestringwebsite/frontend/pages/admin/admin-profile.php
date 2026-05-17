<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

// ── Handle GCash QR Code Upload ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_qr'])) {
    if (isset($_FILES['qr_code_image']) && $_FILES['qr_code_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['qr_code_image']['tmp_name'];
        $fileType = $_FILES['qr_code_image']['type'];
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (in_array($fileType, $allowedTypes)) {
            $targetDir = '../../public/images/';
            
            // Ensure the directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $targetFilePath = $targetDir . 'qr_code.png';
            
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                echo "<script>alert('GCash QR Code uploaded and updated successfully!'); window.location.href='admin-profile.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error moving the uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.');</script>";
        }
    } else {
        echo "<script>alert('No file selected or an upload error occurred.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,500;1,500&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../../../frontend/css/admin/admin-profile.css">
</head>

<body>
    <div id="admin-sidebar">
        <img src="../../public/images/admin/companylogo.png" alt="Company Logo" class="logo">
        <p>
            <a href="../admin-homepage.php"><img src="../../public/images/admin/dashboard icon.png" class="icon">Dashboard</a>
            <a href="../admin/admin-products.php"><img src="../../public/images/admin/products icon.png" class="icon">Products</a>
            <a href="../admin/admin-customers.php"><img src="../../public/images/admin/customers icon.png" class="icon">Customers</a>
            <a href="../admin/admin-notification.php"><img src="../../public/images/admin/Notification bell icon.png" class="icon">Notifications</a>
        </p>
    </div>

    <section id="content">
        <div class="profile-grid">
            <div class="left-cards">
                <div class="profile-card" id="card-1">
                    <div class="avatar-section">
                        <div class="pfpf">
                            <?php
                            $avatar_src = '../../public/images/profile icon.png';
                            if (!empty($user['avatar'])) {
                                $path = __DIR__ . '/../public/images/avatars/' . $user['avatar'];
                                if (file_exists($path)) {
                                    $avatar_src = '../public/images/avatars/' . $user['avatar'];
                                }
                            }
                            ?>
                            <img src="<?= $avatar_src ?>" alt="profile" class="avatar-img">
                        </div>
                        <div class="editbtn">
                            <button class="edit-btn"><a href="Admin-profileEditMode.php"><img src="../../public/images/edit profile icon.png" alt="edit">Edit Profile</a></button>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="row">
                            <div class="label">Name</div>
                            <div class="value name"><h1><?= htmlspecialchars($user['display_name'] ?? ($user['user_name'] ?? '')) ?></h1></div>
                        </div>

                        <div class="row">
                            <div class="label">Username</div>
                            <div class="value username"><strong><?= htmlspecialchars($user['user_name'] ?? '') ?></strong></div>
                        </div>

                        <div class="row bio">
                            <div class="label">Bio</div>
                            <div class="value"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></div>
                        </div>

                        <div class="row">
                            <div class="label">Phone Number</div>
                            <div class="value"><?= htmlspecialchars($user['phone'] ?? '') ?></div>
                        </div>

                        <div class="row">
                            <div class="label">Address</div>
                            <div class="value"><?= nl2br(htmlspecialchars($user['address'] ?? '')) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="profile-card right-card">
                    <div class="account-menu">
                        <div class="menu-item">
                            <span class="menu-icon"><img src="../../public/images/myaccount updated.png" alt="profile icon"></span>
                            <a href="admin-profile.php" class="menu-link">My Account</a>
                        </div>
                        
                        <div class="menu-item">
                            <span class="menu-icon"><img src="../../public/images/admin/qr code icon.png" alt="qr code" class="menu-icon-qr"></span>
                            <a href="#" class="menu-link" onclick="openQrModal(event)">QR Code</a>
                        </div>

                        <div class="menu-item">
                            <span class="menu-icon"></span>
                            <a href="../../../../logout.php" class="menu-link"><strong>Log Out</strong></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="qrModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="popout-card" style="background: #fff; border-radius: 14px; padding: 32px; max-width: 420px; width: 90%; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18); position: relative; text-align: center; box-sizing: border-box;">
            
            <button type="button" onclick="closeQrModal()" style="position: absolute; top: 14px; right: 18px; background: none; border: none; font-size: 22px; cursor: pointer; color: #aaa; transition: color 0.2s;" onmouseover="this.style.color='#6b46c1'" onmouseout="this.style.color='#aaa'">&times;</button>
            
            <h2 style="color: #6b46c1; margin: 0 0 8px; font-family: 'Josefin Sans', sans-serif; font-size: 20px;">Update QR Code</h2>
            <p style="font-size: 13px; color: #666; margin: 0 0 20px; font-family: 'Josefin Sans', sans-serif;">Place your 1x1 GCash QR code below:</p>
            
            <form method="POST" enctype="multipart/form-data" id="qrUploadForm">
                <div id="dropZone" style="border: 2px dashed #b49fdc; padding: 30px 20px; border-radius: 8px; background: #faf5ff; cursor: pointer; margin-bottom: 16px; transition: background 0.2s, border-color 0.2s;">
                    <span id="dropZoneText" style="font-size: 13px; color: #7f56da; font-family: 'Josefin Sans', sans-serif; display: block; font-weight: 500;">Drag & Drop or Click to Select File</span>
                    <input type="file" name="qr_code_image" id="qrFileInput" accept="image/*" style="display: none;" onchange="previewQrFile()">
                </div>
                
                <div id="qrPreviewContainer" style="display: none; margin-bottom: 20px; justify-content: center;">
                    <img id="qrUploadPreview" src="#" alt="QR Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeQrModal()" style="flex: 1; padding: 11px; border: 2px solid #ccc; background: #fff; color: #666; border-radius: 6px; font-weight: 600; font-family: 'Josefin Sans', sans-serif; cursor: pointer; font-size: 14px;">Cancel</button>
                    <button type="submit" name="upload_qr" style="flex: 2; padding: 11px; background: #6b46c1; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-family: 'Josefin Sans', sans-serif; cursor: pointer; font-size: 14px; transition: background 0.2s;" onmouseover="this.style.background='#553c9a'" onmouseout="this.style.background='#6b46c1'">Confirm Image</button>
                </div>
            </form>
        </div>
    </div>

<script>
function openQrModal(event) {
    if (event) event.preventDefault();
    document.getElementById('qrModalOverlay').style.display = 'flex';
}

function closeQrModal() {
    document.getElementById('qrModalOverlay').style.display = 'none';
    // Clear selections on cancellation
    document.getElementById('qrFileInput').value = '';
    document.getElementById('dropZoneText').textContent = 'Drag & Drop or Click to Select File';
    document.getElementById('qrPreviewContainer').style.display = 'none';
}

// Close popout modal dynamically when selecting outer backdrop overlay
document.getElementById('qrModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQrModal();
    }
});

// Drag-and-drop & file selection handlers
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('qrFileInput');
const dropZoneText = document.getElementById('dropZoneText');
const previewContainer = document.getElementById('qrPreviewContainer');
const previewImage = document.getElementById('qrUploadPreview');

if (dropZone) {
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#553c9a';
        dropZone.style.background = '#f3e8ff';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#b49fdc';
        dropZone.style.background = '#faf5ff';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#b49fdc';
        dropZone.style.background = '#faf5ff';
        
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            previewQrFile();
        }
    });
}

function previewQrFile() {
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        dropZoneText.textContent = file.name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
}
</script>
</body>
</html>