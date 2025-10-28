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
  <title>Edit Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:720px">
  <h4 class="mb-3">Edit Menu #<?= (int)$item['id'] ?></h4>
  <form method="post" class="row g-3">
    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
    <div class="col-12">
      <label class="form-label">Nama</label>
      <input class="form-control" name="name" required value="<?= e($item['name']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Kategori</label>
      <select class="form-select" name="category" required>
        <?php foreach (['nasi'=>'Nasi','lauk'=>'Lauk','minum'=>'Minum'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= $item['category']===$k?'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Harga</label>
      <input class="form-control" type="number" min="0" name="price" required value="<?= (int)$item['price'] ?>">
    </div>
    <div class="col-12 form-check">
      <input class="form-check-input" type="checkbox" id="active" name="active" <?= $item['active']?'checked':'' ?>>
      <label class="form-check-label" for="active">Aktif</label>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Simpan</button>
      <a class="btn btn-secondary" href="menu.php">Batal</a>
    </div>
  </form>
</div>
</body>
</html>