<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Booking extends BaseModel {
    public $id;
    public $user_id;
    public $villa_id;
    public $booking_date;
    public $days;           // berapa hari
    public $price_per_day;  // harga per hari
    public $total_payment;  // total = days * price_per_day
    public $payment_method; // cash / transfer
    public $payment_proof;  // path ke file bukti pembayaran (jika transfer)
    public $status;

    public function save() {
        if ($this->id) {
            // update
            $stmt = $this->db->prepare("UPDATE bookings
                SET user_id=?, villa_id=?, booking_date=?, days=?, price_per_day=?, total_payment=?, payment_method=?, payment_proof=?, status=?
                WHERE id=?");
            $stmt->execute([
                $this->user_id,
                $this->villa_id,
                $this->booking_date,
                $this->days,
                $this->price_per_day,
                $this->total_payment,
                $this->payment_method,
                $this->payment_proof,
                $this->status,
                $this->id
            ]);
        } else {
            // insert
            $stmt = $this->db->prepare("INSERT INTO bookings
                (user_id, villa_id, booking_date, days, price_per_day, total_payment, payment_method, payment_proof, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $this->user_id,
                $this->villa_id,
                $this->booking_date,
                $this->days,
                $this->price_per_day,
                $this->total_payment,
                $this->payment_method,
                $this->payment_proof,
                $this->status
            ]);
            $this->id = $this->db->lastInsertId();
        }
        return $this;
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM bookings WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $booking = new Booking();
            $booking->id = $row['id'];
            $booking->user_id = $row['user_id'];
            $booking->villa_id = $row['villa_id'];
            $booking->booking_date = $row['booking_date'];
            $booking->days = $row['days'];
            $booking->price_per_day = $row['price_per_day'];
            $booking->total_payment = $row['total_payment'];
            $booking->payment_method = $row['payment_method'];
            $booking->payment_proof = $row['payment_proof'];
            $booking->status = $row['status'];
            return $booking;
        }
        return null;
    }

    // Method all() tidak berubah, tapi kita perlu ambil data kolom baru juga
    public static function all() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM bookings");
        $results = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $booking = new Booking();
            $booking->id = $row['id'];
            $booking->user_id = $row['user_id'];
            $booking->villa_id = $row['villa_id'];
            $booking->booking_date = $row['booking_date'];
            $booking->days = $row['days'];
            $booking->price_per_day = $row['price_per_day'];
            $booking->total_payment = $row['total_payment'];
            $booking->payment_method = $row['payment_method'];
            $booking->payment_proof = $row['payment_proof'];
            $booking->status = $row['status'];
            $results[] = $booking;
        }
        return $results;
    }
}
