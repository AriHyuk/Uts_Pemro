<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id<=0) { http_response_code(400); exit('ID tidak valid'); }

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name'] ?? '');
  $cat  = $_POST['category'] ?? '';
  $price= (int)($_POST['price'] ?? 0);
  $active = isset($_POST['active']) ? 1 : 0;
  if ($name!=='' && in_array($cat, ['nasi','lauk','minum'], true) && $price>=0) {
    $stmt = $mysqli->prepare("UPDATE menu_items SET name=?, category=?, price=?, active=? WHERE id=?");
    $stmt->bind_param('ssiii', $name, $cat, $price, $active, $id);
    $stmt->execute(); $stmt->close();
    header('Location: menu.php'); exit;
  }
}

$stmt = $mysqli->prepare("SELECT id,name,category,price,active FROM menu_items WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$item) { http_response_code(404); exit('Item tidak ditemukan'); }
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Menu - Resto Family</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container main-container py-4">
  <div class="header-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <h1 class="brand-title-with-emoji">✏️ <span class="gradient-text">Edit Menu</span></h1>
          <div>
              <a class="btn btn-outline-secondary" href="menu.php">⬅️ Kembali</a>
          </div>
      </div>
  </div>

  <div class="main-box">
    <div class="payment-card">
      <h6 class="section-title">Edit Item #<?= (int)$item['id'] ?></h6>
      <form method="post" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
        
        <div class="col-12">
          <label class="form-label fw-semibold">Nama Item</label>
          <input class="form-control" name="name" required value="<?= e($item['name']) ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label fw-semibold">Kategori</label>
          <select class="form-select" name="category" required>
            <?php foreach (['nasi'=>'🍚 Nasi','lauk'=>'🍗 Lauk','minum'=>'🥤 Minum'] as $k=>$v): ?>
              <option value="<?= $k ?>" <?= $item['category']===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label fw-semibold">Harga</label>
          <input class="form-control" type="number" min="0" name="price" required value="<?= (int)$item['price'] ?>">
        </div>
        
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="active" name="active" <?= $item['active']?'checked':'' ?>>
            <label class="form-check-label fw-semibold" for="active">✅ Item Aktif</label>
          </div>
        </div>
        
        <div class="col-12 mt-4">
          <div class="d-flex gap-2">
            <button class="btn btn-success">💾 Simpan Perubahan</button>
            <a class="btn btn-outline-secondary" href="menu.php">❌ Batal</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>