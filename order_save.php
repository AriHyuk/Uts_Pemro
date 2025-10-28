<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/functions.php';

// Re-hitung total di server untuk integritas
$cart = post_json('cart');
$pay = $_POST['pay_method'] ?? 'cash';
$cash = max(0, (int)($_POST['cash'] ?? 0));
$note = trim((string)($_POST['note'] ?? ''));

// Ambil harga valid dari DB
$ids = [];
foreach ($cart as $row) {
  if (!empty($row['nasiId'])) $ids[] = (int)$row['nasiId'];
  foreach (($row['laukIds'] ?? []) as $i) $ids[] = (int)$i;
  foreach (($row['minumIds'] ?? []) as $i) $ids[] = (int)$i;
}
$ids = array_values(array_unique(array_filter($ids)));
$prices = [];
if ($ids) {
  $in = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $mysqli->prepare("SELECT id,name,price FROM menu_items WHERE id IN ($in)");
  // bind dinamis
  $types = str_repeat('i', count($ids));
  $stmt->bind_param($types, ...$ids);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) $prices[(int)$r['id']] = ['name'=>$r['name'],'price'=>(int)$r['price']];
  $stmt->close();
}

$total = 0;
$orderItems = [];

// Flatten tiap baris menjadi item per menu (lebih jelas di struk)
foreach ($cart as $row) {
  if (!empty($row['nasiId']) && !empty($row['nasiQty'])) {
    $id = (int)$row['nasiId'];
    if (isset($prices[$id])) {
      $qty = max(0,(int)$row['nasiQty']);
      $unit = (int)$prices[$id]['price'];
      $sub = $unit * $qty;
      $total += $sub;
      $orderItems[] = ['menu_id'=>$id,'name'=>$prices[$id]['name'],'unit'=>$unit,'qty'=>$qty,'sub'=>$sub];
    }
  }
  if (!empty($row['laukIds']) && !empty($row['laukQty'])) {
    foreach ($row['laukIds'] as $id) {
      $id = (int)$id;
      if (isset($prices[$id])) {
        $qty = max(0,(int)$row['laukQty']);
        $unit = (int)$prices[$id]['price'];
        $sub = $unit * $qty;
        $total += $sub;
        $orderItems[] = ['menu_id'=>$id,'name'=>$prices[$id]['name'],'unit'=>$unit,'qty'=>$qty,'sub'=>$sub];
      }
    }
  }
  if (!empty($row['minumIds']) && !empty($row['minumQty'])) {
    foreach ($row['minumIds'] as $id) {
      $id = (int)$id;
      if (isset($prices[$id])) {
        $qty = max(0,(int)$row['minumQty']);
        $unit = (int)$prices[$id]['price'];
        $sub = $unit * $qty;
        $total += $sub;
        $orderItems[] = ['menu_id'=>$id,'name'=>$prices[$id]['name'],'unit'=>$unit,'qty'=>$qty,'sub'=>$sub];
      }
    }
  }
}

$tax = (int)round($total * 0.10);
$grand = $total + $tax;
if ($pay === 'card') { $cash = $grand; } // kenapa: kartu = bayar pas sesuai grand total
$change = max(0, $cash - $grand);

$mysqli->begin_transaction();
try {
  $stmt = $mysqli->prepare("INSERT INTO orders(total,tax,grand_total,pay_method,cash_received,change_amount,note,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
  $stmt->bind_param('iiisiss', $total, $tax, $grand, $pay, $cash, $change, $note);
  $stmt->execute();
  $orderId = (int)$stmt->insert_id;
  $stmt->close();

  $stmt = $mysqli->prepare("INSERT INTO order_items(order_id,menu_id,item_name,unit_price,qty,subtotal) VALUES (?,?,?,?,?,?)");
  foreach ($orderItems as $it) {
    $stmt->bind_param('iisiii', $orderId, $it['menu_id'], $it['name'], $it['unit'], $it['qty'], $it['sub']);
    $stmt->execute();
  }
  $stmt->close();

  $mysqli->commit();
  header("Location: order_receipt.php?id=".$orderId);
  exit;
} catch (Throwable $e) {
  $mysqli->rollback();
  http_response_code(500);
  echo 'Gagal simpan order.';
}
