<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/*
    Villa mewakili tabel villas di database.
    Memperlihatkan penggunaan array juga, misal
    kita simpan daftar property di array jika mau.
*/

class Villa extends BaseModel {
    public $id;
    public $name;
    public $description;
    public $image;

    public function save() {
        if ($this->id) {
            // update
            $stmt = $this->db->prepare("UPDATE villas SET name=?, description=?, image=? WHERE id=?");
            $stmt->execute([$this->name, $this->description, $this->image, $this->id]);
        } else {
            // insert
            $stmt = $this->db->prepare("INSERT INTO villas (name, description, image) VALUES (?, ?, ?)");
            $stmt->execute([$this->name, $this->description, $this->image]);
            $this->id = $this->db->lastInsertId();
        }
        return $this;
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM villas WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $villa = new Villa();
            $villa->id = $row['id'];
            $villa->name = $row['name'];
            $villa->description = $row['description'];
            $villa->image = $row['image'];
            return $villa;
        }
        return null;
    }

    // Contoh fungsi untuk mengambil semua villa
    public static function all() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM villas");
        $results = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $villa = new Villa();
            $villa->id = $row['id'];
            $villa->name = $row['name'];
            $villa->description = $row['description'];
            $villa->image = $row['image'];
            $results[] = $villa;
        }
        return $results;
    }
}