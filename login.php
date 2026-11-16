<?php
require 'config.php';
if (!empty($_SESSION['admin'])) { header('Location: admin.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = strtolower(trim($_POST['username'] ?? ''));
    $sym  = trim($_POST['sym'] ?? '');
    $priv = str_replace("\r", "", trim($_POST['priv'] ?? ''));

    $adm = $db->query("SELECT username_hash FROM admin LIMIT 1")->fetch();
    $row = $db->query("SELECT enc_sym FROM kunci LIMIT 1")->fetch();

    $okUser = $adm && password_verify($user, $adm['username_hash']);
    $okSym  = hash_equals(SYM_KEY, $sym);
    $okAsim = false;
    $pk = @openssl_pkey_get_private($priv);
    if ($row && $pk) {
        $out = '';
        $okAsim = openssl_private_decrypt(base64_decode($row['enc_sym']), $out, $pk) && hash_equals(SYM_KEY, $out);
    }

    if ($okUser && $okSym && $okAsim) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: admin.php'); exit;
    }
    $err = 'Username, kunci simetris, atau private key salah!';
}
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin</title><link rel="stylesheet" href="style.css">
</head><body>
<div class="hero"><div class="box" style="max-width:520px">
  <h2>🔑 Login Admin</h2>
  <p class="sub">Masuk dengan username, kunci simetris, dan kunci asimetris.</p>
  <?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <label>Username</label>
    <input name="username" required>
    <label>Kunci Simetris (AES)</label>
    <input type="password" name="sym" required>
    <label>Private Key (RSA)</label>
    <textarea name="priv" rows="7" placeholder="-----BEGIN PRIVATE KEY-----" required></textarea>
    <br><br><button class="btn block">Masuk</button>
  </form>
  <hr class="sep">
  <div class="center"><a href="index.php">&larr; Kembali ke landing page</a></div>
</div></div>
</body></html>
