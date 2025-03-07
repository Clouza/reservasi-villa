<?php
namespace App\Models;

use PDO;

/*
    User adalah turunan BaseModel, menerapkan inheritance.
    Polymorphism muncul dari implementasi method2 abstract di BaseModel.
*/

class User extends BaseModel {
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    // Contoh function untuk men-simpan user ke DB
    public function save() {
        // Jika $this->id ada, maka update, kalau tidak ada, insert baru
        if ($this->id) {
            // update
            $stmt = $this->db->prepare("UPDATE users SET name=?, email=?, password=?, role=? WHERE id=?");
            $stmt->execute([$this->name, $this->email, $this->password, $this->role, $this->id]);
        } else {
            // insert
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->name, $this->email, $this->password, $this->role]);
            $this->id = $this->db->lastInsertId();
        }
        return $this;
    }

    // Override method findById
    public static function findById($id) {
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $user = new User();
            $user->id = $row['id'];
            $user->name = $row['name'];
            $user->email = $row['email'];
            $user->password = $row['password'];
            $user->role = $row['role'];
            return $user;
        }
        return null;
    }

    // Contoh pencarian user berdasarkan email
    public static function findByEmail($email) {
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $user = new User();
            $user->id = $row['id'];
            $user->name = $row['name'];
            $user->email = $row['email'];
            $user->password = $row['password'];
            $user->role = $row['role'];
            return $user;
        }
        return null;
    }
}