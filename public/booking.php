<?php
session_start();
require_once __DIR__ . '/config.php';

// Require file inti
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
use App\Controllers\BookingController;
use App\Models\Villa;
use App\Models\Booking;
use App\Models\User;

// Pastikan user login
if (!AuthController::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Pastikan role == 'user', kalau admin redirect
if ($_SESSION['role'] !== 'user') {
    header('Location: admin.php');
    exit;
}

$bookingMessage = '';
if (isset($_POST['book'])) {
    $villa_id = $_POST['villa_id'];
    $date = $_POST['date'];
    $bookingCtrl = new BookingController();
    $newBooking = $bookingCtrl->createBooking($_SESSION['user_id'], $villa_id, $date);
    if ($newBooking) {
        $bookingMessage = "Booking berhasil!";
    } else {
        $bookingMessage = "Gagal booking.";
    }
}

// Ambil semua villa
$villas = Villa::all();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Halaman Booking - User</title>
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

<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white shadow mb-6">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Reservasi Villa</h1>
            <a href="logout.php" class="text-blue-600 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4">Selamat datang, User!</h2>
        <?php if ($bookingMessage): ?>
            <div class="mb-4 text-green-600 font-semibold">
                <?php echo $bookingMessage; ?>
            </div>
        <?php endif; ?>

        <h3 class="text-xl font-semibold mb-2">Daftar Villa</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($villas as $villa): ?>
                <div class="bg-white p-4 rounded shadow">
                    <h4 class="text-lg font-bold mb-2"><?php echo $villa->name; ?></h4>
                    <p class="text-gray-600 mb-2"><?php echo $villa->description; ?></p>
                    <div class="mb-2">
                        <?php if ($villa->image): ?>
                            <img src="uploads/<?php echo $villa->image; ?>" alt="Villa Image" class="w-full h-48 object-cover rounded">
                        <?php else: ?>
                            <div class="bg-gray-200 h-48 flex items-center justify-center text-gray-500">
                                (Belum ada foto)
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Form Booking -->
                    <form method="POST" action="" class="mt-2">
                        <input type="hidden" name="villa_id" value="<?php echo $villa->id; ?>">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Booking:</label>
                        <input type="date" name="date" required class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">
                        <button type="submit" name="book" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                            Book Sekarang
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>