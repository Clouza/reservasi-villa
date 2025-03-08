<?php
/*
    index.php - Landing page + Form Login
*/
session_start();
require_once __DIR__ . '/config.php';

// require_once ... (semua file inti, sama seperti contoh sebelumnya)
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

// Proses login
$message = '';
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $auth = new AuthController();
    $loginSuccess = $auth->login($email, $password);
    if ($loginSuccess) {
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Landing Page - Reservasi Villa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style>
        * {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="h-screen grid md:grid-cols-2">
    <div class="flex justify-center flex-col px-4 sm:px-12 lg:px-24">
        <h1 class="text-2xl font-bold mb-4 text-center">Selamat Datang</h1>
        <p class="text-center text-gray-500 mb-6">Silakan login untuk melanjutkan.</p>

        <?php if ($message): ?>
            <div class="text-red-500 mb-4 text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email:</label>
                <input type="text" name="email" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Masukkan email anda">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Password:</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Masukkan password anda">
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors hover:cursor-pointer">
                Login
            </button>
        </form>
    </div>
    <img class="hidden md:block h-full object-cover" src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Villa">
</body>

</html>