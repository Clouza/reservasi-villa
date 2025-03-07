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
use App\Controllers\AdminController;
use App\Controllers\BookingController;
use App\Models\Villa;
use App\Models\Booking;

// Cek login & role admin
if (!AuthController::isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$adminCtrl = new AdminController();
$bookingCtrl = new BookingController();

// Tambah/Update villa
$message = '';
if (isset($_POST['save_villa'])) {
    // Inisialisasi variabel pesan
    $message = '';
    $filename = null;

    if (!empty($_FILES['villa_image']['name'])) {
        // Cek apakah terjadi error saat upload
        $errorCode = $_FILES['villa_image']['error'];

        if ($errorCode === UPLOAD_ERR_OK) {
            // Tidak ada error, lanjutkan proses upload
            $filename = time() . '_' . $_FILES['villa_image']['name'];
            $tmp_path = $_FILES['villa_image']['tmp_name'];

            // Pindahkan file ke folder uploads
            // Pastikan folder 'uploads' punya permission tulis.
            move_uploaded_file($tmp_path, UPLOAD_PATH . $filename);

        } elseif ($errorCode === UPLOAD_ERR_INI_SIZE) {
            // Error code 1 => File melebihi upload_max_filesize
            $message = "Ukuran file melebihi batas upload_max_filesize di php.ini!";
        } elseif ($errorCode === UPLOAD_ERR_FORM_SIZE) {
            // Error code 2 => file melebihi MAX_FILE_SIZE (jika kita set di form)
            $message = "Ukuran file melebihi batas MAX_FILE_SIZE di form!";
        } else {
            // Untuk error lain, kita bisa tambahkan case lain
            $message = "Terjadi kesalahan saat upload file (Error code: $errorCode).";
        }
    }

    // Jika tidak ada message error, maka lanjut simpan data villa
    if (empty($message)) {
        // Siapkan data array
        $data = [
            'id' => $_POST['id'] ?? null,
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'image' => $filename  // hasil upload
        ];

        $savedVilla = $adminCtrl->saveVilla($data);
        if ($savedVilla) {
            $message = "Data villa berhasil disimpan!";
        } else {
            $message = "Gagal menyimpan villa!";
        }
    }
}

// Hapus villa
if (isset($_GET['delete_villa'])) {
    $villa_id = $_GET['delete_villa'];
    $adminCtrl->deleteVilla($villa_id);
    $message = "Villa telah dihapus.";
}

// Update status booking
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $newStatus = $_POST['new_status'];
    $bookingCtrl->updateStatus($booking_id, $newStatus);
    $message = "Status booking diupdate.";
}

// Ambil semua villa
$villas = $adminCtrl->getAllVillas();

// Ambil semua booking
$allBookings = $bookingCtrl->getAllBookings();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Halaman Admin - Reservasi Villa</title>
</head>
<body>
    <h1>Halaman Admin</h1>
    <a href="logout.php">Logout</a>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Kelola Villa</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="">
        <label>Nama Villa:</label><br>
        <input type="text" name="name"><br><br>

        <label>Deskripsi Villa:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Foto:</label><br>
        <input type="file" name="villa_image"><br><br>

        <button type="submit" name="save_villa">Simpan Villa</button>
    </form>

    <h3>Daftar Villa</h3>
    <ul>
        <?php foreach($villas as $villa): ?>
            <li>
                <strong><?php echo $villa->name; ?></strong> <br>
                Deskripsi: <?php echo $villa->description; ?><br>
                <?php if($villa->image): ?>
                    <img src="uploads/<?php echo $villa->image; ?>" width="100">
                <?php endif; ?><br>
                <a href="?delete_villa=<?php echo $villa->id; ?>" onclick="return confirm('Yakin?')">Hapus Villa</a>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>

    <h2>Daftar Booking</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Villa ID</th>
            <th>Tanggal Booking</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach($allBookings as $booking): ?>
        <tr>
            <td><?php echo $booking->id; ?></td>
            <td><?php echo $booking->user_id; ?></td>
            <td><?php echo $booking->villa_id; ?></td>
            <td><?php echo $booking->booking_date; ?></td>
            <td><?php echo $booking->status; ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="booking_id" value="<?php echo $booking->id; ?>">
                    <select name="new_status">
                        <option value="pending" <?php if($booking->status=='pending') echo 'selected'; ?>>Pending</option>
                        <option value="confirmed" <?php if($booking->status=='confirmed') echo 'selected'; ?>>Confirmed</option>
                        <option value="cancelled" <?php if($booking->status=='cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>