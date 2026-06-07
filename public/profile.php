<?php
require_once 'auth_check.php';
require_login();
require_once 'db_connect.php';

$userId = $_SESSION['user_id'];
$error = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '') {
        $error = 'Nama lengkap tidak boleh kosong.';
    } elseif ($email === '') {
        $error = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    }

    if (!$error) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id');
        $stmt->execute(['email' => $email, 'id' => $userId]);
        if ($stmt->fetch()) {
            $error = 'Email sudah digunakan oleh pengguna lain.';
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
        $stmt->execute(['name' => $name, 'email' => $email, 'id' => $userId]);

        $_SESSION['user_name'] = $name;
        $_SESSION['email'] = $email;

        header('Location: profile.php?success=1');
        exit;
    }
}

$stmt = $pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php?message=login_required');
    exit;
}

if (isset($_GET['success']) && $_GET['success'] === '1') {
    $successMessage = 'Profil berhasil diperbarui.';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Profil - OceanCare</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2ff; color: #1f2937; }
        .container { max-width: 720px; margin: 48px auto; padding: 24px; background: #ffffff; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .button { padding: 10px 18px; border-radius: 8px; background: #0ea5e9; color: #ffffff; text-decoration: none; }
        .message { margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; }
        button { padding: 12px 18px; border: none; border-radius: 10px; background: #0ea5e9; color: #ffffff; cursor: pointer; }
        .meta { margin-top: 24px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Profil Pengguna</h1>
                <p>Atur data akun volunteer / organizer Anda.</p>
            </div>
            <div>
                <a class="button" href="dashboard.php">Kembali ke Dashboard</a>
                <a class="button" href="logout.php" style="background:#ef4444; margin-left:10px;">Logout</a>
            </div>
        </div>

        <?php if ($successMessage): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="profile.php">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($_POST['name'] ?? $user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email']); ?>" required>
            </div>
            <button type="submit">Simpan Perubahan</button>
        </form>

        <div class="meta">
            <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
            <p><strong>Tanggal Registrasi:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($user['created_at']))); ?></p>
        </div>
    </div>
</body>
</html>
