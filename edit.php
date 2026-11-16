<?php
require 'config.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare("SELECT * FROM pesan WHERE id=?");
$st->execute([$id]);
$row = $st->fetch();
if (!$row) { header('Location: admin.php'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $isi = trim($_POST['isi'] ?? '');
    if ($isi === '') {
        $err = 'Isi pesan tidak boleh kosong.';
    } else {
        // dienkripsi ulang dengan AES sebelum disimpan
        $db->prepare("UPDATE pesan SET isi_enkripsi=? WHERE id=?")->execute([enkripsi($isi), $id]);
        header('Location: admin.php?msg=edit'); exit;
    }
}
$nilai = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['isi'] ?? '') : dekripsi($row['isi_enkripsi']);
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Data</title><link rel="stylesheet" href="style.css">
</head><body>
<div class="hero"><div class="box" style="max-width:520px">
  <h2>✏️ Edit Data #<?= (int)$id ?></h2>
  <p class="sub">Isi pesan akan dienkripsi ulang saat disimpan.</p>
  <?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <label>Isi pesan</label>
    <textarea name="isi" rows="5" required><?= e($nilai) ?></textarea>
    <br><br>
    <button class="btn block">Simpan Perubahan</button>
    <br>
    <a class="btn abu block center" href="admin.php">Batal</a>
  </form>
</div></div>
</body></html>
