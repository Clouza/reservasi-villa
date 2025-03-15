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

// Pastikan user login
if (!AuthController::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Pastikan role == 'user'
if ($_SESSION['role'] !== 'user') {
    header('Location: admin.php');
    exit;
}

$bookingMessage = '';
if (isset($_POST['book'])) {
    $villa_id       = $_POST['villa_id'];
    $date           = $_POST['date'];
    $days           = $_POST['days'];
    $price_per_day  = $_POST['price_per_day'];
    $payment_method = $_POST['payment_method'];

    // Upload bukti pembayaran jika transfer
    $payment_proof = null;
    if ($payment_method === 'transfer' && !empty($_FILES['payment_proof']['name'])) {
        $filename = time() . '_' . $_FILES['payment_proof']['name'];
        move_uploaded_file($_FILES['payment_proof']['tmp_name'], UPLOAD_PATH . $filename);
        $payment_proof = $filename;
    }

    $bookingCtrl = new BookingController();
    $newBooking = $bookingCtrl->createBooking(
        $_SESSION['user_id'],
        $villa_id,
        $date,
        $days,
        $price_per_day,
        $payment_method,
        $payment_proof
    );
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Booking - User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white shadow mb-6">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Reservasi Villa</h1>
            <a href="logout.php" class="text-blue-600 hover:underline">Logout</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4">Selamat datang, User!</h2>
        <?php if ($bookingMessage): ?>
            <div class="mb-4 text-green-600 font-semibold"><?php echo $bookingMessage; ?></div>
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
                            <div class="bg-gray-200 h-48 flex items-center justify-center text-gray-500">(Belum ada foto)</div>
                        <?php endif; ?>
                    </div>
                    <!-- Form Booking -->
                    <form method="POST" action="" enctype="multipart/form-data" class="mt-2">
                        <input type="hidden" name="villa_id" value="<?php echo $villa->id; ?>">

                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Booking:</label>
                        <input type="date" name="date" required class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">

                        <label class="block mb-2 text-sm font-medium text-gray-700">Durasi (hari):</label>
                        <input type="number" name="days" value="1" min="1" required class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">

                        <label class="block mb-2 text-sm font-medium text-gray-700">Harga per Hari (Rp):</label>
                        <input type="number" name="price_per_day" value="100000" required class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">

                        <label class="block mb-2 text-sm font-medium text-gray-700">Metode Pembayaran:</label>
                        <select name="payment_method" class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>

                        <label class="block mb-2 text-sm font-medium text-gray-700">Bukti Pembayaran (jika transfer):</label>
                        <input type="file" name="payment_proof" class="border p-2 w-full mb-2 rounded focus:outline-none focus:ring">

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
