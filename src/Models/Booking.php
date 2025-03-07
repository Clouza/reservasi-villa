<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/*
    Booking mewakili data pemesanan / reservasi.
*/

class Booking extends BaseModel {
    public $id;
    public $user_id;
    public $villa_id;
    public $booking_date;
    public $status;

    public function save() {
        if ($this->id) {
            // update
            $stmt = $this->db->prepare("UPDATE bookings SET user_id=?, villa_id=?, booking_date=?, status=? WHERE id=?");
            $stmt->execute([$this->user_id, $this->villa_id, $this->booking_date, $this->status, $this->id]);
        } else {
            // insert
            $stmt = $this->db->prepare("INSERT INTO bookings (user_id, villa_id, booking_date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->user_id, $this->villa_id, $this->booking_date, $this->status]);
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
            $booking->status = $row['status'];
            return $booking;
        }
        return null;
    }

    // Contoh method: ambil semua booking
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
            $booking->status = $row['status'];
            $results[] = $booking;
        }
        return $results;
    }
}