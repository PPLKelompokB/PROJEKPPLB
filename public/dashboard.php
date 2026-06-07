<?php
require_once 'auth_check.php';
require_login();

$userName = $_SESSION['user_name'] ?? 'Pengguna';
$userRole = $_SESSION['role'] ?? 'Role tidak tersedia';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard OceanCare</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .container { max-width: 760px; margin: 64px auto; padding: 24px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .button { padding: 10px 16px; border: none; border-radius: 8px; background: #0ea5e9; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard OceanCare</h1>
                <p>Halo, <?php echo htmlspecialchars($userName); ?> (<?php echo htmlspecialchars($userRole); ?>)</p>
            </div>
            <div>
                <a class="button" href="profile.php" style="margin-right:8px;">Profil</a>
                <a class="button" href="logout.php">Logout</a>
            </div>
        </div>
        <p>Halaman ini dilindungi dan hanya dapat diakses oleh pengguna yang sudah login.</p>
        <p>Jika logout berhasil, kembali ke <code>login.php</code> dan sesi akan dihentikan sepenuhnya.</p>
    </div>
</body>
</html>
