<?php
namespace App\Core;

/*
    Kelas Database ini menggunakan PDO untuk koneksi ke MySQL.
    Kalian bisa menambahkan method lain sesuai kebutuhan.
*/

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // Membaca constant DB_HOST, DB_NAME, dsb. dari config
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Bisa tambahkan setAttribute lain jika perlu
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    /*
        Method ini akan mengembalikan instance Database.
        Menggunakan pola singleton.
    */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*
        Mendapatkan objek PDO
    */
    public function getConnection() {
        return $this->connection;
    }
}