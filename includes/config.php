<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'joki');

// Cookie Configuration
define('COOKIE_NAME', 'user_cookie');
define('COOKIE_EXPIRY', 3600); // Cookie akan kedaluwarsa setelah 1 jam (3600 detik)

// Session Configuration
define('SESSION_TIMEOUT', 3600); // Sesi akan kedaluwarsa setelah 1 jam (3600 detik)

// Site Configuration
define('SITE_URL', 'http://localhost/joki/');
define('SITE_NAME', 'joki');

// File Upload Configuration
define('UPLOADS_DIR', __DIR__ . '../assets/data/file'); // Direktori untuk menyimpan file yang diunggah
define('DATA_DIR', UPLOADS_DIR . '../assets/data/file'); // Direktori untuk menyimpan file data
define('FILE_DIR', UPLOADS_DIR . '../assets/data/file'); // Direktori untuk menyimpan file lainnya

// Path Configuration
define('ASSETS_PATH', SITE_URL . 'assets/'); // Path ke direktori assets
define('CSS_PATH', ASSETS_PATH . 'css/'); // Path ke direktori CSS
define('JS_PATH', ASSETS_PATH . 'js/'); // Path ke direktori JS
define('IMG_PATH', ASSETS_PATH . 'img/'); // Path ke direktori gambar
define('FONTS_PATH', ASSETS_PATH . 'fonts/'); // Path ke direktori font

define('VENDOR_PATH', SITE_URL . 'vendor/');
define('BOOTSTRAP_PATH', VENDOR_PATH . 'bootstrap/');
define('BOOTSICO_PATH', VENDOR_PATH . 'bootstrap-icons/');
define('DATATABLES_PATH', VENDOR_PATH . 'datatables/');
define('FONTAWE_PATH', VENDOR_PATH . 'fontawesome-free/');
define('JQUERY_PATH', VENDOR_PATH . 'jquery/');
define('JQUERYE_PATH', VENDOR_PATH . 'jquery-easing/');
define('LEAFLET_PATH', VENDOR_PATH . 'leaflet/');
define('PURECOUN_PATH', VENDOR_PATH . 'purecounter/');

// Other Configurations
define('DEBUG_MODE', true); // Setel ke false di lingkungan produksi untuk menghindari tampilan error