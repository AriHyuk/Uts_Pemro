<?php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';                 // default XAMPP kosong
$DB_NAME = 'uts';

$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  exit('Gagal konek DB: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8'));
}
$mysqli->set_charset('utf8mb4');