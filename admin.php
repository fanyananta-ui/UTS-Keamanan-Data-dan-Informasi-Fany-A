<?php
require 'config.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

// hapus data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus' && csrf_ok()) {
    $db->prepare("DELETE FROM pesan WHERE id=?")->execute([(int)$_POST['id']]);
    header('Location: admin.php?msg=hapus'); exit;
}

$data    = $db->query("SELECT * FROM pesan ORDER BY id DESC")->fetchAll();
$total   = count($data);
$hariIni = $db->query("SELECT COUNT(*) FROM pesan WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$terakhir = $data[0]['created_at'] ?? '-';
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard Admin</title><link rel="stylesheet" href="style.css">
</head><body>
<div class="layout">
  <aside class="side">
    <h3>🔐 Enkripsi App</h3>
    <a class="aktif" href="admin.php">📊 Dashboard</a>
    <a href="index.php" target="_blank">📝 Landing Page</a>
    <a class="bawah" href="logout.php">🚪 Logout</a>
  </aside>
  <main class="main">
    <h1>Dashboard Admin</h1>
    <div class="sub">Semua data hasil enkripsi yang tersimpan di database.</div>

    <?php if ($msg === 'hapus'): ?><div class="alert ok">Data berhasil dihapus.</div><?php endif; ?>
    <?php if ($msg === 'edit'):  ?><div class="alert ok">Data berhasil diperbarui (dienkripsi ulang).</div><?php endif; ?>

    <div class="cards">
      <div class="card"><div class="l">Total Data</div><div class="v"><?= (int)$total ?></div></div>
      <div class="card"><div class="l">Data Hari Ini</div><div class="v"><?= (int)$hariIni ?></div></div>
      <div class="card"><div class="l">Data Terakhir</div><div class="v kecil"><?= e($terakhir) ?></div></div>
      <div class="card"><div class="l">Algoritma</div><div class="v kecil">AES-256-CBC<br>RSA-2048</div></div>
    </div>

    <div class="panel">
      <h2>Data Terenkripsi</h2>
      <div class="tw">
      <table>
        <tr><th>ID</th><th>Data Terenkripsi (AES-256)</th><th>Hasil Dekripsi</th><th>Waktu</th><th>Aksi</th></tr>
        <?php if (!$data): ?>
          <tr><td colspan="5">Belum ada data. Simpan pesan dari landing page.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $d): ?>
        <tr>
          <td><?= (int)$d['id'] ?></td>
          <td class="enc"><?= e($d['isi_enkripsi']) ?></td>
          <td class="dec"><?= e(dekripsi($d['isi_enkripsi'])) ?></td>
          <td><?= e($d['created_at']) ?></td>
          <td><div class="aksi">
            <a class="mini edit" href="edit.php?id=<?= (int)$d['id'] ?>">✏️ Edit</a>
            <form method="post" onsubmit="return confirm('Hapus data ini?')">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
              <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
              <button class="mini hapus">🗑️ Hapus</button>
            </form>
          </div></td>
        </tr>
        <?php endforeach; ?>
      </table>
      </div>
    </div>
  </main>
</div>
</body></html>
