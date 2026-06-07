<?php
// Konfigurasi koneksi MySQL untuk Laragon.
// Ubah sesuai nama database dan kredensial Anda.
$dbHost = '127.0.0.1';
$dbName = 'oceancare';
$dbUser = 'root';
$dbPass = '';
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    die('Koneksi database gagal: ' . $exception->getMessage());
}
