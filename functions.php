<?php
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function rupiah(int $n): string { return 'Rp' . number_format($n, 0, ',', '.'); }

/** Ambil seluruh menu aktif, optionally dikelompokkan per kategori */
function get_menu(mysqli $db): array {
  $res = $db->query("SELECT id,name,category,price FROM menu_items WHERE active=1 ORDER BY category, id");
  $data = ['nasi'=>[], 'lauk'=>[], 'minum'=>[]];
  while ($row = $res->fetch_assoc()) {
    $data[$row['category']][] = $row;
  }
  return $data;
}

function post_json(string $key): array {
  $raw = $_POST[$key] ?? '[]';
  $arr = json_decode($raw, true);
  return is_array($arr) ? $arr : [];
}