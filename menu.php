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
  <title>Kelola Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Kelola Menu</h4>
    <div><a class="btn btn-secondary" href="index.php">Kembali ke Order</a></div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h6>Tambah Menu</h6>
      <form method="post" class="row g-2">
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Nama item" required></div>
        <div class="col-md-3">
          <select class="form-select" name="category" required>
            <option value="">Kategori...</option>
            <option value="nasi">Nasi</option>
            <option value="lauk">Lauk</option>
            <option value="minum">Minum</option>
          </select>
        </div>
        <div class="col-md-3"><input class="form-control" type="number" min="0" name="price" placeholder="Harga" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light"><tr><th>ID</th><th>Nama</th><th>Kategori</th><th class="text-end">Harga</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= e($r['name']) ?></td>
            <td><?= e(ucfirst($r['category'])) ?></td>
            <td class="text-end"><?= rupiah((int)$r['price']) ?></td>
            <td><?= $r['active'] ? 'Ya' : 'Tidak' ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="menu_edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
              <form action="menu_delete.php" method="post" style="display:inline" onsubmit="return confirm('Hapus item ini?')">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
