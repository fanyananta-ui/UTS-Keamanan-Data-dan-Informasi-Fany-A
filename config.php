<?php
session_start();
define('SYM_KEY', 'KunciRahasia1234567890abcdefghij'); // tepat 32 karakter
$db = new PDO('mysql:host=localhost;dbname=db_enkripsi;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function enkripsi($teks) {
    $iv = random_bytes(16);
    $c = openssl_encrypt($teks, 'aes-256-cbc', SYM_KEY, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $c);
}
function dekripsi($data) {
    $raw = base64_decode($data);
    return openssl_decrypt(substr($raw, 16), 'aes-256-cbc', SYM_KEY, OPENSSL_RAW_DATA, substr($raw, 0, 16));
}

// token sederhana untuk melindungi aksi hapus/edit
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
function csrf_ok() {
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
