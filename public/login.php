<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
if (!empty($_GET['status']) && $_GET['status'] === 'logged_out') {
    $message = 'Logout berhasil. Silakan login kembali.';
} elseif (!empty($_GET['message']) && $_GET['message'] === 'login_required') {
    $message = 'Harap login terlebih dahulu untuk mengakses halaman ini.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login OceanCare</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #111827; }
        .container { max-width: 420px; margin: 80px auto; padding: 24px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); }
        .message { margin-bottom: 16px; padding: 12px; border-radius: 8px; background: #e0f2fe; color: #034f84; }
        .button { display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border: none; border-radius: 8px; background: #0ea5e9; color: white; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login OceanCare</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <p>Ini adalah halaman login sederhana untuk fitur logout PHP native. Silakan implementasikan proses autentikasi sesuai sistem Anda.</p>
        <p>Jika menggunakan sistem autentikasi PHP native, setelah login pastikan memasukkan data berikut ke dalam <code>$_SESSION</code>:</p>
        <ul>
            <li><code>user_id</code></li>
            <li><code>user_name</code></li>
            <li><code>email</code></li>
            <li><code>role</code></li>
        </ul>
        <a class="button" href="dashboard.php">Contoh Dashboard</a>
    </div>
</body>
</html>
