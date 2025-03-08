<?php
session_start();
require_once __DIR__ . '/config.php';

// require file inti
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

// Cek login & role admin
if (!AuthController::isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$adminCtrl = new AdminController();
$bookingCtrl = new BookingController();

$message = '';
// Tambah/Update villa
if (isset($_POST['save_villa'])) {
    $filename = null;
    if (!empty($_FILES['villa_image']['name'])) {
        $filename = time() . '_' . $_FILES['villa_image']['name'];
        move_uploaded_file($_FILES['villa_image']['tmp_name'], UPLOAD_PATH . $filename);
    }

    $data = [
        'id' => $_POST['id'] ?? null,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'image' => $filename
    ];
    $savedVilla = $adminCtrl->saveVilla($data);
    if ($savedVilla) {
        $message = "Data villa berhasil disimpan!";
    } else {
        $message = "Gagal menyimpan villa!";
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Halaman Admin - Reservasi Villa</title>
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
            <h1 class="text-xl font-bold">Panel Admin</h1>
            <a href="logout.php" class="text-red-600 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4">
        <?php if ($message): ?>
            <div class="mb-4 text-green-600 font-semibold">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Form Kelola Villa -->
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-bold mb-4">Kelola Villa</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nama Villa:</label>
                        <input type="text" name="name" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Masukkan nama villa">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi Villa:</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" rows="4" placeholder="Deskripsi singkat villa"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Foto Villa:</label>
                        <input type="file" name="villa_image" class="w-full">
                    </div>

                    <button type="submit" name="save_villa" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors hover:cursor-pointer">
                        Simpan Villa
                    </button>
                </form>
            </div>

            <!-- Daftar Villa -->
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-bold mb-4">Daftar Villa</h2>
                <ul>
                    <?php foreach ($villas as $villa): ?>
                        <li class="mb-4 border-b pb-2">
                            <div class="flex items-center justify-between">
                                <strong class="block text-gray-700"><?php echo $villa->name; ?></strong>
                                <a href="?delete_villa=<?php echo $villa->id; ?>" onclick="return confirm('Yakin?')" class="block text-red-600 hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                            <p class="text-sm text-gray-500"><?php echo $villa->description; ?></p>
                            <?php if ($villa->image): ?>
                                <img src="uploads/<?php echo $villa->image; ?>" alt="Villa Image" class="mt-2 w-full h-32 object-cover rounded">
                            <?php else: ?>
                                <div class="mt-2 bg-gray-200 text-center text-gray-500 py-8">
                                    (Tidak ada foto)
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Daftar Booking -->
        <div class="bg-white p-4 rounded shadow mb-8">
            <h2 class="text-lg font-bold mb-4">Daftar Booking</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-2 px-4 border">ID</th>
                            <th class="py-2 px-4 border">User ID</th>
                            <th class="py-2 px-4 border">Villa ID</th>
                            <th class="py-2 px-4 border">Tanggal Booking</th>
                            <th class="py-2 px-4 border">Status</th>
                            <th class="py-2 px-4 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBookings as $booking): ?>
                            <tr>
                                <td class="py-2 px-4 border"><?php echo $booking->id; ?></td>
                                <td class="py-2 px-4 border"><?php echo $booking->user_id; ?></td>
                                <td class="py-2 px-4 border"><?php echo $booking->villa_id; ?></td>
                                <td class="py-2 px-4 border"><?php echo $booking->booking_date; ?></td>
                                <td class="py-2 px-4 border"><?php echo $booking->status; ?></td>
                                <td class="py-2 px-4 border">
                                    <form method="POST" action="" class="flex items-center">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking->id; ?>">
                                        <select name="new_status" class="border rounded px-2 py-1 mr-2 focus:outline-none focus:ring">
                                            <option value="pending" <?php if ($booking->status == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="confirmed" <?php if ($booking->status == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                            <option value="cancelled" <?php if ($booking->status == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="bg-green-600 text-white py-1 px-3 rounded hover:bg-green-700 transition-colors">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>