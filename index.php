<?php
require 'config.php';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && trim($_POST['isi'] ?? '') !== '') {
    $st = $db->prepare("INSERT INTO pesan (isi_enkripsi) VALUES (?)");
    $st->execute([enkripsi(trim($_POST['isi']))]);
    $ok = true;
}
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Enkripsi App</title><link rel="stylesheet" href="style.css">
</head><body>
<div class="hero"><div class="box">
  <h1>🔐 Enkripsi App</h1>
  <p class="sub">Sistem informasi dengan data terenkripsi. Pesan yang kamu simpan diamankan dengan AES-256 sebelum masuk database.</p>
  <div class="badges"><span class="badge">AES-256</span><span class="badge">RSA-2048</span><span class="badge">Login Berbasis Kunci</span></div>
  <?php if ($ok): ?><div class="alert ok">Data tersimpan dalam bentuk terenkripsi.</div><?php endif; ?>
  <form method="post">
    <label>Tulis pesan</label>
    <textarea name="isi" rows="4" placeholder="Tulis pesan yang ingin disimpan..." required></textarea>
    <br><br><button class="btn block">Simpan Terenkripsi</button>
  </form>
  <hr class="sep">
  <a class="btn ghost block center" href="login.php">Masuk Halaman Admin</a>
</div></div>
</body></html>
