<?php
require __DIR__ . '/koneksi.php';
$id = (int)($_POST['id'] ?? 0);
if ($id<=0) { http_response_code(400); exit('ID tidak valid'); }
$stmt = $mysqli->prepare("DELETE FROM menu_items WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
header('Location: menu.php');
exit;