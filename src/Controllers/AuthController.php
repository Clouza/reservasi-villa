<?php
namespace App\Controllers;

/*
    AuthController mengurus proses autentikasi, misalnya login.
*/

use App\Models\User;

class AuthController {

    /*
        Fungsi login untuk memverifikasi email dan password.
        Jika valid, simpan data user di session.
    */
    public function login($email, $password) {
        // Cari user berdasarkan email
        $user = User::findByEmail($email);
        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user->password)) {
                // Jika benar, simpan ke session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['role'] = $user->role;
                return true;
            }
        }
        return false;
    }

    /*
        Fungsi cek apakah user sudah login.
    */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /*
        Fungsi cek role, misal untuk hak akses admin.
    */
    public static function isAdmin() {
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }

    /*
        Fungsi logout
    */
    public function logout() {
        session_destroy();
    }
}