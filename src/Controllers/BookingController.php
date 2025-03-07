<?php
namespace App\Controllers;

use App\Models\Booking;

class BookingController {
    /*
        Fungsi untuk menambahkan booking baru
    */
    public function createBooking($user_id, $villa_id, $booking_date) {
        $booking = new Booking();
        $booking->user_id = $user_id;
        $booking->villa_id = $villa_id;
        $booking->booking_date = $booking_date;
        $booking->status = 'pending';
        $booking->save();
        return $booking;
    }

    /*
        Fungsi untuk update status booking
    */
    public function updateStatus($booking_id, $newStatus) {
        $booking = Booking::findById($booking_id);
        if ($booking) {
            $booking->status = $newStatus;
            $booking->save();
            return $booking;
        }
        return null;
    }

    /*
        Fungsi untuk menghapus booking
    */
    public function deleteBooking($booking_id) {
        $booking = Booking::findById($booking_id);
        if ($booking) {
            // Contoh cara hapus: Query manual
            $db = $booking->db;
            $stmt = $db->prepare("DELETE FROM bookings WHERE id=?");
            $stmt->execute([$booking_id]);
        }
    }

    /*
        Ambil semua booking
    */
    public function getAllBookings() {
        return Booking::all();
    }
}