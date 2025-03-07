CREATE DATABASE IF NOT EXISTS `reservasi_villa`;
USE `reservasi_villa`;

-- DROP TABLE jika sudah ada, agar migrasi bersih
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `villas`;
DROP TABLE IF EXISTS `users`;

/*
    Tabel users untuk menyimpan data user (admin atau user).
    role: "admin" atau "user"
*/
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/*
    Tabel villas untuk menyimpan data villa.
    image: path/filename dari file foto yang diupload
*/
CREATE TABLE `villas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `image` VARCHAR(255),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/*
    Tabel bookings untuk menyimpan data booking / reservasi.
    status: "pending", "confirmed", "cancelled", dll. (sederhana saja)
*/
CREATE TABLE `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `villa_id` INT NOT NULL,
    `booking_date` DATE NOT NULL,
    `status` VARCHAR(50) DEFAULT 'pending',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE
);

/* SEED DATA */
/*
    Insert user admin (email=admin@localhost, password=admin123 [di-hash]).
    Insert user biasa (email=user@localhost, password=user123 [di-hash]).
*/
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES
('Administrator', 'admin@localhost', '$2y$10$Y4AgqKkAub47IMwoZLweve98yAjgpF4rjHerysZZKiqh5DSJpzMkm', 'admin'),
('Pengguna Biasa', 'user@localhost', '$2y$10$qtTmQD3UTMAs/J/HfctUuu9CopJQp0vnpKtaJvVXaZd3gyzv9UEE.', 'user');

/*
    Insert contoh data villa.
    Image dibiarkan NULL, bisa diupdate nanti melalui upload.
*/
INSERT INTO `villas` (`name`, `description`, `image`)
VALUES
('Villa Pantai Indah', 'Villa dekat pantai dengan pemandangan laut yang indah.', NULL),
('Villa Pegunungan Asri', 'Villa di pegunungan, udara sejuk dan pemandangan hijau.', NULL);

/*
    Insert contoh data booking (opsional).
    Di sini user dengan ID 2 (user biasa) booking villa ID 1.
*/
INSERT INTO `bookings` (`user_id`, `villa_id`, `booking_date`, `status`)
VALUES
(2, 1, '2025-03-10', 'pending');