<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Villa;

class AdminController {
    /*
        Fungsi untuk menambahkan atau mengupdate data villa
        $data adalah array berisi info villa.
    */
    public function saveVilla($data) {
        $villa = null;

        if (!empty($data['id'])) {
            $villa = Villa::findById($data['id']);
            if (!$villa) {
                $villa = new Villa();
            }
        } else {
            $villa = new Villa();
        }

        $villa->name = $data['name'] ?? '';
        $villa->description = $data['description'] ?? '';
        $villa->image = $data['image'] ?? null; // path/foto

        $villa->save();
        return $villa;
    }

    /*
        Fungsi untuk mengambil semua villa
    */
    public function getAllVillas() {
        return Villa::all();
    }

    /*
        Fungsi untuk menghapus villa
    */
    public function deleteVilla($id) {
        $villa = Villa::findById($id);
        if ($villa) {
            if ($villa->image && file_exists(UPLOAD_PATH . $villa->image)) {
                unlink(UPLOAD_PATH . $villa->image);
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM villas WHERE id=?");
            $stmt->execute([$id]);
            // Opsional: hapus file foto di folder uploads jika ingin
        }
    }
}