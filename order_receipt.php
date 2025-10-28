<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID tidak valid'); }

// Ambil order
$hdr = $mysqli->prepare("SELECT id,total,tax,grand_total,pay_method,cash_received,change_amount,note,created_at FROM orders WHERE id=?");
$hdr->bind_param('i', $id);
$hdr->execute();
$order = $hdr->get_result()->fetch_assoc();
$hdr->close();
if (!$order) { http_response_code(404); exit('Order tidak ditemukan'); }

// Ambil items
$it = $mysqli->prepare("SELECT item_name,unit_price,qty,subtotal FROM order_items WHERE order_id=? ORDER BY id");
$it->bind_param('i', $id);
$it->execute();
$items = $it->get_result()->fetch_all(MYSQLI_ASSOC);
$it->close();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Struk #<?= (int)$order['id'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>@media print {.noprint{display:none}}</style>
</head>
<body class="p-4">
<div class="container" style="max-width:720px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">RESTO FAMILY</h4>
    <div class="noprint">
      <a class="btn btn-secondary btn-sm" href="index.php">Order Baru</a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
    </div>
  </div>
  <div class="border rounded p-3">
    <div class="d-flex justify-content-between">
      <div>No. Struk: <strong>#<?= (int)$order['id'] ?></strong></div>
      <div><?= e($order['created_at']) ?></div>
    </div>
    <hr>
    <table class="table table-sm">
      <thead class="table-light"><tr><th>Item</th><th class="text-end">Harga</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($items as $r): ?>
          <tr>
            <td><?= e($r['item_name']) ?></td>
            <td class="text-end"><?= rupiah((int)$r['unit_price']) ?></td>
            <td class="text-end"><?= (int)$r['qty'] ?></td>
            <td class="text-end"><?= rupiah((int)$r['subtotal']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="d-flex justify-content-end">
      <div style="min-width:280px">
        <div class="d-flex justify-content-between"><span>Total</span><strong><?= rupiah((int)$order['total']) ?></strong></div>
        <div class="d-flex justify-content-between"><span>PPN 10%</span><strong><?= rupiah((int)$order['tax']) ?></strong></div>
        <hr>
        <div class="d-flex justify-content-between"><span>Grand Total</span><strong><?= rupiah((int)$order['grand_total']) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Metode</span><strong><?= e(strtoupper($order['pay_method'])) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Bayar</span><strong><?= rupiah((int)$order['cash_received']) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Kembali</span><strong><?= rupiah((int)$order['change_amount']) ?></strong></div>
        <?php if ($order['note']): ?><div class="mt-2"><em>Catatan:</em> <?= e($order['note']) ?></div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>