<?php
namespace App\Controllers;

use App\Models\Booking;

class BookingController {
    /*
        Buat booking baru
        Di sini kita tambahkan parameter untuk days, price, payment_method, payment_proof, dsb.
    */
    public function createBooking($user_id, $villa_id, $booking_date, $days, $price_per_day, $payment_method, $payment_proof) {
        $booking = new Booking();
        $booking->user_id = $user_id;
        $booking->villa_id = $villa_id;
        $booking->booking_date = $booking_date;
        $booking->days = $days;
        $booking->price_per_day = $price_per_day;
        // total
        $booking->total_payment = $days * $price_per_day;
        $booking->payment_method = $payment_method;
        $booking->payment_proof = $payment_proof;
        $booking->status = 'pending';
        $booking->save();
        return $booking;
    }

    public function updateStatus($booking_id, $newStatus) {
        $booking = Booking::findById($booking_id);
        if ($booking) {
            $booking->status = $newStatus;
            $booking->save();
            return $booking;
        }
        return null;
    }

    public function deleteBooking($booking_id) {
        $booking = Booking::findById($booking_id);
        if ($booking) {
            $db = $booking->db;
            $stmt = $db->prepare("DELETE FROM bookings WHERE id=?");
            $stmt->execute([$booking_id]);
        }
    }

    public function getAllBookings() {
        return Booking::all();
    }
}
