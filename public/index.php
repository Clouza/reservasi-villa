<?php
/*
    index.php - Landing page + Form Login
    Gunakan session_start() agar bisa menggunakan session.
*/
session_start();

require_once __DIR__ . '/config.php';

// Kita butuh autoload manual atau require semua file.
// Sederhananya, require file inti satu per satu (atau gunakan composer autoload kalau mau).
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/ModelInterface.php';
require_once __DIR__ . '/../src/Models/BaseModel.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Villa.php';
require_once __DIR__ . '/../src/Models/Booking.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/BookingController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Controllers\AuthController;

// Jika user sudah login, redirect ke halaman sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: booking.php');
        exit;
    }
}

// Proses login jika ada submit
$message = '';
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $auth = new AuthController();
    $loginSuccess = $auth->login($email, $password);
    if ($loginSuccess) {
        // Berhasil login, cek role
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: booking.php');
        }
        exit;
    } else {
        $message = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Landing Page - Reservasi Villa</title>
</head>
<body>
    <h1>Selamat Datang di Website Reservasi Villa</h1>
    <p>Silakan login terlebih dahulu.</p>

    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email:</label><br>
        <input type="text" name="email"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>