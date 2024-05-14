<?php
// Panggil file konfigurasi
require_once 'config.php';

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Memeriksa koneksi
if ($conn->connect_error) {
  // Jika koneksi gagal
  if (DEBUG_MODE) {
    die("Koneksi gagal: " . $conn->connect_error);
  } else {
    // Redirect ke halaman error
    header('Location: ' . SITE_URL . '404.html');
    exit;
  }
}

// Set karakter set
$conn->set_charset("utf8");