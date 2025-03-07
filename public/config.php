<?php
/*
    config.php - file konfigurasi database
    Silakan sesuaikan username, password, dan nama DB.
*/

define('DB_HOST', 'localhost');
define('DB_NAME', 'reservasi_villa');
define('DB_USER', 'admin'); // default: root
define('DB_PASS', 'admin'); // kosongkan jika tidak ada password

// Opsi path untuk upload foto
define('UPLOAD_PATH', __DIR__ . '/uploads/');