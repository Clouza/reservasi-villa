<?php
session_start();
require_once __DIR__ . '/config.php';

// Require file inti (sama seperti di index.php)
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/ModelInterface.php';
require_once __DIR__ . '/../src/Models/BaseModel.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Villa.php';
require_once __DIR__ . '/../src/Models/Booking.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/BookingController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

use App\Models\User;

$message = '';

if (isset($_POST['register'])) {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    // Cek apakah email sudah ada?
    $existingUser = User::findByEmail($email);
    if ($existingUser) {
        $message = "Email sudah terdaftar!";
    } else {
        // Buat user baru
        $newUser = new User();
        $newUser->name = $name;
        $newUser->email = $email;
        // Hash password
        $newUser->password = password_hash($pass, PASSWORD_BCRYPT);
        // Default role = 'user'
        $newUser->role = 'user';
        $newUser->save();

        $message = "Registrasi berhasil! Silakan login.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>
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
        <h1  class="text-2xl font-bold mb-4 text-center">Registrasi Akun Baru</h1>
        <?php if ($message): ?>
            <p class="text-center text-gray-500 mb-6"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nama Lengkap:</label>
                <input type="text" name="name" required placeholder="Masukkan nama anda" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email:</label>
                <input type="email" name="email" required placeholder="Masukkan email anda" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Password:</label>
                <input type="password" name="password" required placeholder="Masukkan password anda" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">
            </div>

            <button type="submit" name="register" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors hover:cursor-pointer">Daftar</button>
        </form>

        <p class="mt-4">Sudah punya akun? <a href="index.php" class="underline">Login di sini</a></p>
    </div>
    <img class="hidden md:block h-full object-cover" src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Villa">
</body>
</html>
