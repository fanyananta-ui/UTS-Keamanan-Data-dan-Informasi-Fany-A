<?php
require 'config.php';
$sudah  = $db->query("SELECT COUNT(*) FROM admin")->fetchColumn() > 0;
$boleh  = !$sudah || isset($_GET['reset']);
$private = null; $err = ''; $username = '';

if ($boleh && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(trim($_POST['username'] ?? ''));
    if (strlen($username) < 4) {
        $err = 'Username minimal 4 karakter.';
    } else {
        $config = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        // cari openssl.cnf (Laragon / XAMPP di Windows)
        $kandidat = array_merge(
            [dirname(ini_get('extension_dir')) . '/extras/ssl/openssl.cnf'],
            glob('C:/laragon/bin/php/*/extras/ssl/openssl.cnf') ?: [],
            glob('C:/laragon/bin/apache/*/conf/openssl.cnf') ?: [],
            ['C:/xampp/apache/conf/openssl.cnf', 'C:/xampp/php/extras/ssl/openssl.cnf']
        );
        foreach ($kandidat as $f) { if (file_exists($f)) { $config['config'] = $f; break; } }

        $res = openssl_pkey_new($config);
        if (!$res) die('Gagal membuat kunci: ' . openssl_error_string());
        openssl_pkey_export($res, $private, null, $config);
        $public = openssl_pkey_get_details($res)['key'];
        openssl_public_encrypt(SYM_KEY, $enc, $public);

        $db->exec("DELETE FROM kunci");
        $db->prepare("INSERT INTO kunci (public_key, enc_sym) VALUES (?, ?)")
           ->execute([$public, base64_encode($enc)]);

        // username disimpan sebagai hash: tidak bisa dibaca dari database
        $db->exec("DELETE FROM admin");
        $db->prepare("INSERT INTO admin (username_hash) VALUES (?)")
           ->execute([password_hash($username, PASSWORD_DEFAULT)]);
    }
}
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Setup</title><link rel="stylesheet" href="style.css">
</head><body>
<div class="hero"><div class="box" style="max-width:560px">
<h2>⚙️ Setup Admin</h2>
<?php if ($private): ?>
  <div class="alert ok">Setup berhasil. Simpan data login di bawah ini!</div>
  <label>Username</label>
  <input value="<?= e($username) ?>" readonly>
  <p class="sub">Username disimpan dalam bentuk hash, jadi <b>tidak bisa dilihat lagi</b> di database. Kalau lupa, jalankan setup ulang.</p>
  <label>Kunci Simetris (AES)</label>
  <input value="<?= e(SYM_KEY) ?>" readonly>
  <label>Private Key (RSA) - hanya tampil sekali</label>
  <textarea rows="10" readonly><?= e($private) ?></textarea>
  <br><br><a class="btn block center" href="login.php">Ke Halaman Login</a>
<?php elseif (!$boleh): ?>
  <div class="alert warn">Setup sudah pernah dijalankan, jadi halaman ini dikunci.</div>
  <p class="sub">Kalau lupa username atau private key, buat ulang lewat <a href="?reset=1">setup ulang</a> (kunci dan username lama tidak berlaku lagi).</p>
  <a class="btn ghost block center" href="login.php">Ke Halaman Login</a>
<?php else: ?>
  <p class="sub">Buat username admin. Kunci RSA akan dibuat otomatis.</p>
  <?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Username admin</label>
    <input name="username" placeholder="mis. fany_admin" required>
    <br><br><button class="btn block">Buat Admin &amp; Kunci</button>
  </form>
<?php endif; ?>
</div></div>
</body></html>
