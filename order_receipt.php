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
  <title>Struk #<?= (int)$order['id'] ?> - Resto Family</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container main-container py-4">
  <div class="header-card noprint">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h1 class="brand-title-with-emoji">🧾 <span class="gradient-text">Struk Pembayaran</span></h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="index.php">🛒 Order Baru</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Struk</button>
      </div>
    </div>
  </div>

  <div class="receipt-card">
    <!-- HEADER STRUK -->
    <div class="receipt-header">
      <div class="receipt-title-with-emoji">🍽️ <span class="gradient-text">RESTO FAMILY</span></div>
      <div class="text-muted">Jl. Sudirman No. 123, Jakarta Pusat</div>
      <div class="text-muted">Telp: (021) 1234-5678</div>
    </div>
    
    <!-- INFO ORDER -->
    <div class="receipt-info">
      <div>
        <strong>No. Struk:</strong> #<?= (int)$order['id'] ?>
      </div>
      <div>
        <strong>Tanggal:</strong> <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?>
      </div>
    </div>
    
    <!-- ITEMS -->
    <div class="table-responsive">
      <table class="table table-sm">
        <thead class="table-light">
          <tr>
            <th>Item</th>
            <th class="text-end">Harga</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $r): ?>
            <tr>
              <td><?= e($r['item_name']) ?></td>
              <td class="text-end"><?= rupiah((int)$r['unit_price']) ?></td>
              <td class="text-center"><?= (int)$r['qty'] ?></td>
              <td class="text-end"><strong><?= rupiah((int)$r['subtotal']) ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <!-- TOTAL & PEMBAYARAN -->
    <div class="receipt-total">
      <div class="summary-row">
        <span>Total Pesanan:</span>
        <strong><?= rupiah((int)$order['total']) ?></strong>
      </div>
      <div class="summary-row">
        <span>PPN (10%):</span>
        <strong><?= rupiah((int)$order['tax']) ?></strong>
      </div>
      <div class="summary-row total">
        <span>Grand Total:</span>
        <strong><?= rupiah((int)$order['grand_total']) ?></strong>
      </div>
      <div class="summary-row">
        <span>Metode Bayar:</span>
        <strong><?= e(strtoupper($order['pay_method'])) ?></strong>
      </div>
      <div class="summary-row">
        <span>Bayar:</span>
        <strong><?= rupiah((int)$order['cash_received']) ?></strong>
      </div>
      <div class="summary-row">
        <span>Kembali:</span>
        <strong style="color: #38ef7d;"><?= rupiah((int)$order['change_amount']) ?></strong>
      </div>
      <?php if ($order['note']): ?>
        <div class="mt-3 p-2 bg-light rounded">
          <small><strong>Catatan:</strong> <?= e($order['note']) ?></small>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- FOOTER -->
    <div class="receipt-footer">
      <div>Terima kasih atas kunjungan Anda</div>
      <div>*** Struk ini sebagai bukti pembayaran ***</div>
    </div>
  </div>
</div>
</body>
</html>