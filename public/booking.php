<?php
session_start();
require_once __DIR__ . '/config.php';
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

if (!AuthController::isLoggedIn()) {
    // Jika belum login, balik ke index
    header('Location: index.php');
    exit;
}

// Jika role bukan user, redirect ke admin
if ($_SESSION['role'] !== 'user') {
    header('Location: admin.php');
    exit;
}

// Proses booking
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

// Ambil booking user saat ini
$currentUserId = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Halaman Booking - User</title>
</head>
<body>
    <h1>Halaman Booking Villa</h1>
    <p>Selamat datang, Anda login sebagai user.</p>
    <a href="logout.php">Logout</a>

    <?php if ($bookingMessage): ?>
        <p><?php echo $bookingMessage; ?></p>
    <?php endif; ?>

    <h2>Daftar Villa</h2>
    <ul>
        <?php foreach($villas as $villa): ?>
            <li>
                <strong><?php echo $villa->name; ?></strong><br>
                Deskripsi: <?php echo $villa->description; ?><br>
                Foto:
                <?php if ($villa->image): ?>
                    <img src="uploads/<?php echo $villa->image; ?>" alt="Villa Image" width="100">
                <?php else: ?>
                    (Belum ada foto)
                <?php endif; ?><br>

                <!-- Form booking -->
                <form method="POST" action="">
                    <input type="hidden" name="villa_id" value="<?php echo $villa->id; ?>">
                    <label>Tanggal Booking:</label>
                    <input type="date" name="date" required>
                    <button type="submit" name="book">Book Sekarang</button>
                </form>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
</body>
</html>