<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/*
    BaseModel mengimplementasikan ModelInterface.
    Di sini kita mendefinisikan method abstract untuk di-override,
    sekaligus menambahkan magic method (overloading).
*/

abstract class BaseModel implements ModelInterface {
    protected $db; // properti untuk menyimpan koneksi DB

    public function __construct() {
        // Ambil koneksi DB via singleton
        $this->db = Database::getInstance()->getConnection();
    }

    /*
        __call adalah contoh overloading di PHP.
        Jika method tidak ditemukan, kode di sini akan dijalankan.
        Kita bisa memanfaatkan ini untuk berbagai keperluan.
    */
    public function __call($name, $arguments) {
        // Contoh sederhana menampilkan pesan:
        echo "Method '$name' tidak ditemukan di class " . __CLASS__ . "!\n";
    }

    /*
        Karena interface mewajibkan save() dan findById(),
        kita tetap definisikan abstract, agar turunan mewujudkan detailnya.
    */
    abstract public function save();

    public static function findById($id) {
        // Karena ini static method, kita tidak punya $this,
        // maka kita perlu instance DB secara manual:
        $db = Database::getInstance()->getConnection();
        return null;
        // Nanti akan di-override oleh child class
    }
}