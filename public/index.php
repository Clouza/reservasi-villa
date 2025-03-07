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

    <style>
        p, a, h1, h2, li, label {
            color: white;
        }

        .bg-container {
            position: absolute;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        .bg-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4); /* Opacity hitam rendah */
        }

        .bg-img {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            display: block;
        }
    </style>
</head>
<body>
    <div class="bg-container">
        <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/71/7b/30/one-bedroom-royal-pool.jpg?w=1000&h=-1&s=1" alt="background" class="bg-img">
    </div>
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