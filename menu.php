<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  // Tambah cepat
  $name = trim($_POST['name'] ?? '');
  $cat  = $_POST['category'] ?? '';
  $price= (int)($_POST['price'] ?? 0);
  if ($name!=='' && in_array($cat, ['nasi','lauk','minum'], true) && $price>=0) {
    $stmt = $mysqli->prepare("INSERT INTO menu_items(name,category,price,active) VALUES (?,?,?,1)");
    $stmt->bind_param('ssi', $name, $cat, $price);
    $stmt->execute(); $stmt->close();
    header('Location: menu.php'); exit;
  }
}
$rows = $mysqli->query("SELECT id,name,category,price,active FROM menu_items ORDER BY category, id")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Menu - Resto Family</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container main-container py-4">
  <div class="header-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <h1 class="brand-title-with-emoji">📋 <span class="gradient-text">Kelola Menu</span></h1>
          <div>
              <a class="btn btn-outline-secondary" href="index.php">⬅️ Kembali ke Order</a>
          </div>
      </div>
  </div>

  <div class="main-box">
    <!-- FORM TAMBAH MENU -->
    <div class="payment-card mb-4">
      <h6>➕ Tambah Menu Cepat</h6>
      <form method="post" class="row g-3">
        <div class="col-md-4">
          <input class="form-control" name="name" placeholder="Nama item" required>
        </div>
        <div class="col-md-3">
          <select class="form-select" name="category" required>
            <option value="">Pilih Kategori...</option>
            <option value="nasi">🍚 Nasi</option>
            <option value="lauk">🍗 Lauk</option>
            <option value="minum">🥤 Minum</option>
          </select>
        </div>
        <div class="col-md-3">
          <input class="form-control" type="number" min="0" name="price" placeholder="Harga" required>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100">Tambah</button>
        </div>
      </form>
    </div>

    <!-- TABEL MENU -->
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th class="text-center">Kategori</th>
            <th class="text-end">Harga</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><strong>#<?= (int)$r['id'] ?></strong></td>
              <td><?= e($r['name']) ?></td>
              <td class="text-center">
                <span class="badge 
                  <?= $r['category'] === 'nasi' ? 'bg-warning' : 
                    ($r['category'] === 'lauk' ? 'bg-success' : 'bg-info') ?>">
                  <?= e(ucfirst($r['category'])) ?>
                </span>
              </td>
              <td class="text-end"><strong><?= rupiah((int)$r['price']) ?></strong></td>
              <td class="text-center">
                <span class="badge <?= $r['active'] ? 'bg-success' : 'bg-secondary' ?>">
                  <?= $r['active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td class="text-center">
                <div class="d-flex gap-2 justify-content-center">
                  <a class="btn btn-sm btn-outline-primary" href="menu_edit.php?id=<?= (int)$r['id'] ?>">✏️ Edit</a>
                  <form action="menu_delete.php" method="post" onsubmit="return confirm('Hapus item ini?')">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">🗑️ Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
            <tr class="empty-state">
              <td colspan="6">📝 Belum ada data menu. Silakan tambah menu baru.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>