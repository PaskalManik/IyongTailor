-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Des 2024 pada 09.15
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msbd`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`` PROCEDURE `AddProduct` (IN `p_product_name` VARCHAR(255), IN `p_product_slider` VARCHAR(50), IN `p_product_price` DECIMAL(10,2), IN `p_product_stock` INT, IN `p_product_img` VARCHAR(255), IN `p_product_preview` TEXT, IN `p_product_description` TEXT)   BEGIN
    INSERT INTO products (
        product_name, 
        product_slider, 
        product_price, 
        product_stock, 
        product_img, 
        product_preview, 
        product_description
    )
    VALUES (
        p_product_name, 
        p_product_slider, 
        p_product_price, 
        p_product_stock, 
        p_product_img, 
        p_product_preview, 
        p_product_description
    );
END$$

CREATE DEFINER=`` PROCEDURE `insert_request` (IN `in_nama_barang` VARCHAR(255), IN `in_jenis_kelamin` ENUM('Laki-laki','Perempuan'), IN `in_ukuran_baju` ENUM('S','M','L','XL','XXL'), IN `in_ukuran_lengan` ENUM('Pendek','Panjang'), IN `in_jumlah` INT, IN `in_saran` TEXT, IN `in_gambar` VARCHAR(255), IN `in_bukti_pembayaran` VARCHAR(255), IN `in_user_id` INT, IN `in_menu_id` INT)   BEGIN
    -- Insert data ke tabel request
    INSERT INTO request (
        nama, 
        jenis_kelamin, 
        ukuran_baju, 
        ukuran_lengan, 
        jumlah, 
        saran, 
        gambar, 
        bukti_pembayaran, 
        user_id, 
        menu_id
    )
    VALUES (
        in_nama_barang, 
        in_jenis_kelamin, 
        in_ukuran_baju, 
        in_ukuran_lengan, 
        in_jumlah, 
        in_saran, 
        in_gambar, 
        in_bukti_pembayaran, 
        in_user_id, 
        in_menu_id
    );
END$$

CREATE DEFINER=`` PROCEDURE `RegisterUser` (IN `p_firstname` VARCHAR(50), IN `p_lastname` VARCHAR(50), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(255), IN `p_contact_no` VARCHAR(20), IN `p_role` VARCHAR(20), OUT `p_verification_code` INT, OUT `p_status` VARCHAR(50))   BEGIN
    DECLARE email_exists INT;
    DECLARE contact_exists INT;

    -- Cek apakah email sudah ada di database
    SELECT COUNT(*) INTO email_exists
    FROM users
    WHERE email = p_email;

    -- Cek apakah nomor HP sudah ada di database
    SELECT COUNT(*) INTO contact_exists
    FROM users
    WHERE contact_no = p_contact_no;

    IF email_exists > 0 THEN
        -- Jika email sudah digunakan
        SET p_status = 'Email already exists';
    ELSEIF contact_exists > 0 THEN
        -- Jika nomor HP sudah digunakan
        SET p_status = 'Contact number already exists';
    ELSE
        -- Generate kode verifikasi 6 digit
        SET p_verification_code = FLOOR(100000 + RAND() * 899999);

        -- Tambahkan pengguna baru ke tabel users
        INSERT INTO users (firstname, lastname, email, password, contact_no, is_verified, verification_code, role)
        VALUES (p_firstname, p_lastname, p_email, p_password, p_contact_no, 0, p_verification_code, p_role);

        -- Set status berhasil
        SET p_status = 'User registered successfully';
    END IF;
END$$

--
-- Fungsi
--
CREATE DEFINER=`` FUNCTION `gajiEmployee` (`jumlah_diambil` INT, `harga_barang` DECIMAL(10,2)) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE gaji DECIMAL(10,2);
    SET gaji = (jumlah_diambil * harga_barang) * 0.18 ;
    RETURN gaji;
END$$

CREATE DEFINER=`` FUNCTION `HitungTotalHarga` (`product_price` DECIMAL(10,2), `quantity` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    RETURN product_price * quantity;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id` int(5) NOT NULL,
  `quantity` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `id`, `quantity`) VALUES
(56, 22, 101, 1);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `cart_view`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `cart_view` (
`id` int(5)
,`user_id` int(11)
,`product_name` varchar(150)
,`quantity` int(10)
,`product_price` int(5)
,`product_img` varchar(600)
,`TotalHarga` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `full_order_view`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `full_order_view` (
`order_id` int(50)
,`order_status` varchar(10)
,`order_date` varchar(20)
,`firstname` varchar(100)
,`lastname` varchar(100)
,`address` varchar(200)
,`city` varchar(50)
,`pincode` varchar(50)
,`payment_mode` varchar(50)
,`user_id` int(11)
,`TotalHarga` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `gaji_view`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `gaji_view` (
`source` varchar(7)
,`total_pendapatan` decimal(42,0)
,`pendapatan_pemilik` decimal(44,1)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
--

CREATE TABLE `logs` (
  `activity_id` int(11) NOT NULL,
  `action_type` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `affected_data` text NOT NULL,
  `nilai_lama` text DEFAULT NULL,
  `nilai_baru` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `action_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `logs`
--

INSERT INTO `logs` (`activity_id`, `action_type`, `table_name`, `affected_data`, `nilai_lama`, `nilai_baru`, `user_id`, `action_date`) VALUES
(1, 'DELETE', 'request', 'request_id: 29', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-11 22:18:15'),
(18, 'INSERT', 'orders', 'order_id: 88', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"OAskoad\", \"lastname\": \"Paskal\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"121213\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-15 10:48:49\", \"order_status\": \"Pending\"}', 22, '2024-12-15 16:48:49'),
(25, 'DELETE', 'request', 'request_id: 21', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 29983, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-16 18:34:09'),
(27, 'DELETE', 'products', 'id: 93', '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 106, \"product_img\": \"product_img/44d0066c9183557d88c121b03ad7f2c1pro42.jpg\", \"product_preview\": \"a:4:{i:0;s:65:\\\"product_img/preview_img/44d0066c9183557d88c121b03ad7f2c1pro42.jpg\\\";i:1;s:56:\\\"product_img/preview_img/44d0066c9183557d88c121b03ad7f2c1\\\";i:2;s:56:\\\"product_img/preview_img/44d0066c9183557d88c121b03ad7f2c1\\\";i:3;s:56:\\\"product_img/preview_img/44d0066c9183557d88c121b03ad7f2c1\\\";}\", \"product_description\": \"Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit culpa quas dolore pariatur soluta veniam esse sunt officiis rerum nulla nostrum nesciunt, maiores harum aut, natus fugit, officia dolorum maxime!\"}', NULL, 23, '2024-12-16 20:02:31'),
(28, 'INSERT', 'products', '100', NULL, '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 5, \"product_img\": \"product_img/70a438ac4a2d922d00131581262ffc67pro42.jpg\", \"product_preview\": \"a:1:{i:0;s:65:\\\"product_img/preview_img/70a438ac4a2d922d00131581262ffc67pro42.jpg\\\";}\", \"product_description\": \"lorem\"}', 23, '2024-12-16 23:49:57'),
(29, 'DELETE', 'products', 'id: 100', '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 5, \"product_img\": \"product_img/70a438ac4a2d922d00131581262ffc67pro42.jpg\", \"product_preview\": \"a:1:{i:0;s:65:\\\"product_img/preview_img/70a438ac4a2d922d00131581262ffc67pro42.jpg\\\";}\", \"product_description\": \"lorem\"}', NULL, 23, '2024-12-16 23:50:19'),
(30, 'UPDATE', 'orders', 'order_id: 88', '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"OAskoad\", \"lastname\": \"Paskal\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"121213\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-15 10:48:49\", \"order_status\": \"Pending\"}', '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"OAskoad\", \"lastname\": \"Paskal\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"121213\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-15 10:48:49\", \"order_status\": \"Complete\"}', 22, '2024-12-17 00:00:56'),
(34, 'INSERT', 'menu', 'menu_id: 10', NULL, '{\"nama_barang\": \"Jaket\", \"harga_barang\": 12000}', 23, '2024-12-17 00:14:48'),
(35, 'UPDATE', 'menu', 'menu_id: 10', '{\"nama_barang\": \"Jaket\", \"harga_barang\": 12000}', '{\"nama_barang\": \"Jaket\", \"harga_barang\": 14000}', 23, '2024-12-17 00:17:59'),
(36, 'DELETE', 'menu', 'menu_id: 10', '{\"nama_barang\": \"Jaket\", \"harga_barang\": 14000}', NULL, 23, '2024-12-17 00:18:03'),
(37, 'INSERT', 'menu', 'menu_id: 11', NULL, '{\"nama_barang\": \"Jaket\", \"harga_barang\": 10000}', 23, '2024-12-17 00:18:14'),
(38, 'DELETE', 'menu', 'menu_id: 11', '{\"nama_barang\": \"Jaket\", \"harga_barang\": 10000}', NULL, 23, '2024-12-17 00:18:17'),
(39, 'INSERT', 'request', '32', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 6, \"gambar\": \"\", \"saran\": \"Bordir\", \"status\": \"pending\", \"created_at\": \"2024-12-17 11:13:53\", \"updated_at\": \"2024-12-17 11:13:53\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-17 11:13:53'),
(40, 'INSERT', 'request', '33', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"\", \"saran\": \"Keren\", \"status\": \"pending\", \"created_at\": \"2024-12-17 13:01:43\", \"updated_at\": \"2024-12-17 13:01:43\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-17 13:01:43'),
(41, 'INSERT', 'request', '34', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 5, \"gambar\": \"pro29.jpg\", \"saran\": \"Kerenkan\", \"status\": \"pending\", \"created_at\": \"2024-12-17 13:27:33\", \"updated_at\": \"2024-12-17 13:27:33\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-17 13:27:33'),
(42, 'INSERT', 'request', '35', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 8, \"gambar\": \"pro22.jpg\", \"saran\": \"ga\", \"status\": \"pending\", \"created_at\": \"2024-12-17 13:31:26\", \"updated_at\": \"2024-12-17 13:31:26\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-17 13:31:26'),
(43, 'INSERT', 'request', '36', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro32.jpg\", \"saran\": \"kece\", \"status\": \"pending\", \"created_at\": \"2024-12-17 13:38:34\", \"updated_at\": \"2024-12-17 13:38:34\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-17 13:38:34'),
(44, 'INSERT', 'request', '37', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 8, \"gambar\": \"pro27.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-17 13:50:24\", \"updated_at\": \"2024-12-17 13:50:24\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-17 13:50:24'),
(45, 'DELETE', 'products', 'id: 94', '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 30, \"product_img\": \"product_img/a5f1dda58f9b6f957ecd341cf2f2fd40pro42.jpg\", \"product_preview\": \"a:4:{i:0;s:65:\\\"product_img/preview_img/a5f1dda58f9b6f957ecd341cf2f2fd40pro42.jpg\\\";i:1;s:65:\\\"product_img/preview_img/a5f1dda58f9b6f957ecd341cf2f2fd40pro41.jpg\\\";i:2;s:65:\\\"product_img/preview_img/a5f1dda58f9b6f957ecd341cf2f2fd40pro42.jpg\\\";i:3;s:65:\\\"product_img/preview_img/a5f1dda58f9b6f957ecd341cf2f2fd40pro41.jpg\\\";}\", \"product_description\": \"Lorem ipsum dolor sit amet consectetur, adipisicing elit. Maiores ullam dolorem quos? Ipsum odit amet labore rem tenetur distinctio veritatis sit? Illum rem et quis nemo, eligendi aspernatur explicabo dolore!\"}', NULL, 23, '2024-12-17 20:02:01'),
(46, 'INSERT', 'products', '101', NULL, '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 30, \"product_img\": \"0\", \"product_preview\": \"a:4:{i:0;s:65:\\\"product_img/preview_img/57cc4d506219d04f5fcf8ecb15e5f4b0pro42.jpg\\\";i:1;s:65:\\\"product_img/preview_img/57cc4d506219d04f5fcf8ecb15e5f4b0pro41.jpg\\\";i:2;s:65:\\\"product_img/preview_img/57cc4d506219d04f5fcf8ecb15e5f4b0pro42.jpg\\\";i:3;s:65:\\\"product_img/preview_img/57cc4d506219d04f5fcf8ecb15e5f4b0pro41.jpg\\\";}\", \"product_description\": \"Lorem ipsum dolor sit amet consectetur, adipisicing elit. Optio, pariatur deserunt vero, quasi sequi nobis eveniet ipsam id eius placeat delectus porro sapiente facere aperiam excepturi ab, non recusandae? Repellendus.\"}', 23, '2024-12-17 20:02:52'),
(47, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 15}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 14}', 0, '2024-12-17 20:23:41'),
(48, 'INSERT', 'orders', 'order_id: 89', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Naurah\", \"lastname\": \"Alya\", \"email\": \"naurah@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"121213\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-17 14:31:04\", \"order_status\": \"Pending\"}', 22, '2024-12-17 20:31:04'),
(49, 'INSERT', 'request', '38', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro6.jpg\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:14:42\", \"updated_at\": \"2024-12-18 12:14:42\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:14:42'),
(50, 'INSERT', 'request', '39', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"saran\": \"oko\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:18:23\", \"updated_at\": \"2024-12-18 12:18:23\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:18:23'),
(51, 'INSERT', 'request', '40', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"saran\": \"oko\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:21:24\", \"updated_at\": \"2024-12-18 12:21:24\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:21:24'),
(52, 'INSERT', 'request', '41', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro6.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:23:41\", \"updated_at\": \"2024-12-18 12:23:41\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:23:41'),
(53, 'INSERT', 'request', '42', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro6.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:25:34\", \"updated_at\": \"2024-12-18 12:25:34\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:25:34'),
(54, 'INSERT', 'request', '43', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro6.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:30:35\", \"updated_at\": \"2024-12-18 12:30:35\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:30:35'),
(55, 'INSERT', 'request', '44', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro1.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:38:26\", \"updated_at\": \"2024-12-18 12:38:26\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 12:38:26'),
(56, 'DELETE', 'request', 'request_id: 39', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(57, 'DELETE', 'request', 'request_id: 40', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(58, 'DELETE', 'request', 'request_id: 41', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"jumlah\": 8, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(59, 'DELETE', 'request', 'request_id: 42', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"jumlah\": 8, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(60, 'DELETE', 'request', 'request_id: 43', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"jumlah\": 8, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(61, 'DELETE', 'request', 'request_id: 44', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 12:38:47'),
(62, 'INSERT', 'request', '45', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro6.jpg\", \"saran\": \"\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:40:57\", \"updated_at\": \"2024-12-18 12:40:57\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:40:57'),
(63, 'INSERT', 'request', '46', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro6.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"\", \"status\": \"pending\", \"created_at\": \"2024-12-18 12:44:05\", \"updated_at\": \"2024-12-18 12:44:05\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 12:44:05'),
(64, 'INSERT', 'request', '47', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:34:59\", \"updated_at\": \"2024-12-18 14:34:59\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:34:59'),
(65, 'INSERT', 'request', '48', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:40:43\", \"updated_at\": \"2024-12-18 14:40:43\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:40:43'),
(66, 'INSERT', 'request', '49', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:42:55\", \"updated_at\": \"2024-12-18 14:42:55\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:42:55'),
(67, 'INSERT', 'request', '50', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:49:12\", \"updated_at\": \"2024-12-18 14:49:12\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:49:12'),
(68, 'INSERT', 'request', '51', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:52:07\", \"updated_at\": \"2024-12-18 14:52:07\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:52:07'),
(69, 'INSERT', 'request', '52', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:55:19\", \"updated_at\": \"2024-12-18 14:55:19\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:55:19'),
(70, 'INSERT', 'request', '53', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"pro6.jpg\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:58:36\", \"updated_at\": \"2024-12-18 14:58:36\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:58:36'),
(71, 'INSERT', 'request', '54', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oe\", \"status\": \"pending\", \"created_at\": \"2024-12-18 14:59:47\", \"updated_at\": \"2024-12-18 14:59:47\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 14:59:47'),
(72, 'INSERT', 'request', '55', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro4.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:03:30\", \"updated_at\": \"2024-12-18 15:03:30\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:03:30'),
(73, 'INSERT', 'request', '56', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro4.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:05:19\", \"updated_at\": \"2024-12-18 15:05:19\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:05:19'),
(74, 'INSERT', 'request', '57', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"pro4.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:07:25\", \"updated_at\": \"2024-12-18 15:07:25\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:07:25'),
(75, 'INSERT', 'request', '58', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"1734509581\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:13:01\", \"updated_at\": \"2024-12-18 15:13:01\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 15:13:01'),
(76, 'INSERT', 'request', '59', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"1734509876\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:17:56\", \"updated_at\": \"2024-12-18 15:17:56\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 15:17:56'),
(77, 'INSERT', 'request', '60', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:21:03\", \"updated_at\": \"2024-12-18 15:21:03\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 15:21:03'),
(78, 'INSERT', 'request', '61', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"1734510165\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:22:45\", \"updated_at\": \"2024-12-18 15:22:45\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 15:22:45'),
(79, 'INSERT', 'request', '62', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:30:18\", \"updated_at\": \"2024-12-18 15:30:18\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 15:30:18'),
(80, 'DELETE', 'request', 'request_id: 54', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(81, 'DELETE', 'request', 'request_id: 55', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 8, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(82, 'DELETE', 'request', 'request_id: 56', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 8, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(83, 'DELETE', 'request', 'request_id: 57', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 8, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(84, 'DELETE', 'request', 'request_id: 58', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(85, 'DELETE', 'request', 'request_id: 59', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(86, 'DELETE', 'request', 'request_id: 60', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(87, 'DELETE', 'request', 'request_id: 61', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(88, 'DELETE', 'request', 'request_id: 62', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:30:42'),
(89, 'DELETE', 'request', 'request_id: 38', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"jumlah\": 8, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(90, 'DELETE', 'request', 'request_id: 45', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(91, 'DELETE', 'request', 'request_id: 46', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(92, 'DELETE', 'request', 'request_id: 47', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(93, 'DELETE', 'request', 'request_id: 48', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(94, 'DELETE', 'request', 'request_id: 49', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(95, 'DELETE', 'request', 'request_id: 50', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(96, 'DELETE', 'request', 'request_id: 51', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(97, 'DELETE', 'request', 'request_id: 52', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(98, 'DELETE', 'request', 'request_id: 53', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 15:35:01'),
(99, 'INSERT', 'request', '63', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:40:27\", \"updated_at\": \"2024-12-18 15:40:27\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:40:27'),
(100, 'INSERT', 'request', '64', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:42:33\", \"updated_at\": \"2024-12-18 15:42:33\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:42:33'),
(101, 'INSERT', 'request', '65', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:47:48\", \"updated_at\": \"2024-12-18 15:47:48\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:47:48'),
(102, 'INSERT', 'request', '66', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro6.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:50:37\", \"updated_at\": \"2024-12-18 15:50:37\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:50:37'),
(103, 'INSERT', 'request', '67', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"\", \"bukti_pembayaran\": \"0\", \"saran\": \"ok\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:55:07\", \"updated_at\": \"2024-12-18 15:55:07\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:55:07'),
(104, 'INSERT', 'request', '68', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 15:57:43\", \"updated_at\": \"2024-12-18 15:57:43\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 15:57:43'),
(105, 'INSERT', 'request', '69', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 16:00:58\", \"updated_at\": \"2024-12-18 16:00:58\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 16:00:58'),
(106, 'INSERT', 'request', '70', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": \"0\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 16:14:36\", \"updated_at\": \"2024-12-18 16:14:36\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-18 16:14:36'),
(107, 'INSERT', 'request', '71', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"bukti_pembayaran\": null, \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 16:56:22\", \"updated_at\": \"2024-12-18 16:56:22\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-18 16:56:22'),
(108, 'INSERT', 'request', '80', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:08:52\", \"updated_at\": \"2024-12-18 17:08:52\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-18 17:08:52'),
(109, 'INSERT', 'request', '81', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:25:45\", \"updated_at\": \"2024-12-18 17:25:45\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-18 17:25:45'),
(110, 'INSERT', 'request', '82', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:27:23\", \"updated_at\": \"2024-12-18 17:27:23\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-18 17:27:23'),
(111, 'INSERT', 'request', '83', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:27:52\", \"updated_at\": \"2024-12-18 17:27:52\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-18 17:27:52'),
(112, 'INSERT', 'request', '84', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:29:23\", \"updated_at\": \"2024-12-18 17:29:23\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-18 17:29:23'),
(113, 'INSERT', 'request', '85', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 7, \"gambar\": \"pro5.jpg\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-18 17:30:02\", \"updated_at\": \"2024-12-18 17:30:02\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-18 17:30:02'),
(114, 'INSERT', 'menu', 'menu_id: 12', NULL, '{\"nama_barang\": \"Coba\", \"harga_barang\": 15000}', 23, '2024-12-18 17:38:39'),
(115, 'DELETE', 'menu', 'menu_id: 12', '{\"nama_barang\": \"Coba\", \"harga_barang\": 15000}', NULL, 23, '2024-12-18 17:38:49'),
(116, 'UPDATE', 'menu', 'menu_id: 1', '{\"nama_barang\": \"Celana\", \"harga_barang\": 18000}', '{\"nama_barang\": \"Celan\", \"harga_barang\": 18000}', 23, '2024-12-18 17:42:41'),
(117, 'UPDATE', 'menu', 'menu_id: 1', '{\"nama_barang\": \"Celan\", \"harga_barang\": 18000}', '{\"nama_barang\": \"Celana\", \"harga_barang\": 18000}', 23, '2024-12-18 17:42:46'),
(118, 'INSERT', 'products', '102', NULL, '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 30, \"product_img\": \"0\", \"product_preview\": \"a:2:{i:0;s:65:\\\"product_img/preview_img/578ac40a8ab529d41dc24595e21a2c99pro42.jpg\\\";i:1;s:65:\\\"product_img/preview_img/578ac40a8ab529d41dc24595e21a2c99pro41.jpg\\\";}\", \"product_description\": \"Lorem Ipsum\"}', 23, '2024-12-18 19:06:26'),
(119, 'DELETE', 'products', 'id: 102', '{\"product_name\": \"Seragam Anak TK\", \"product_slider\": \"Product\", \"product_price\": 125000, \"product_stock\": 30, \"product_img\": \"0\", \"product_preview\": \"a:2:{i:0;s:65:\\\"product_img/preview_img/578ac40a8ab529d41dc24595e21a2c99pro42.jpg\\\";i:1;s:65:\\\"product_img/preview_img/578ac40a8ab529d41dc24595e21a2c99pro41.jpg\\\";}\", \"product_description\": \"Lorem Ipsum\"}', NULL, 23, '2024-12-18 19:06:43'),
(120, 'UPDATE', 'products', 'product_id: 101', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 30}', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 30}', 0, '2024-12-18 19:07:00'),
(121, 'UPDATE', 'products', 'product_id: 85', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 6}', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 5}', 0, '2024-12-18 19:13:43'),
(122, 'UPDATE', 'products', 'product_id: 86', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 3}', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 2}', 0, '2024-12-18 19:23:22'),
(123, 'UPDATE', 'products', 'product_id: 86', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 2}', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 1}', 0, '2024-12-18 19:23:57'),
(124, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 14}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 13}', 0, '2024-12-18 19:24:03'),
(125, 'DELETE', 'request', 'request_id: 16', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 20:48:33'),
(126, 'DELETE', 'request', 'request_id: 20', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 20:48:33'),
(127, 'DELETE', 'request', 'request_id: 22', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"XL\", \"jumlah\": 0, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 20:48:33'),
(128, 'DELETE', 'request', 'request_id: 23', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 20:48:33'),
(129, 'DELETE', 'request', 'request_id: 24', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 0, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-18 20:48:49'),
(130, 'DELETE', 'request', 'request_id: 27', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 20:49:39'),
(131, 'DELETE', 'request', 'request_id: 71', '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 7, \"menu_id\": 1, \"user_id\": 22}', NULL, 22, '2024-12-18 20:50:32'),
(132, 'DELETE', 'request', 'request_id: 80', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-18 20:50:32'),
(133, 'DELETE', 'request', 'request_id: 82', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-18 20:50:32'),
(134, 'DELETE', 'request', 'request_id: 83', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-18 20:50:32'),
(135, 'DELETE', 'request', 'request_id: 84', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-18 20:50:32'),
(136, 'DELETE', 'request', 'request_id: 85', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-18 20:50:32'),
(137, 'DELETE', 'request', 'request_id: 65', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-18 20:50:50'),
(138, 'UPDATE', 'products', 'product_id: 86', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 1}', '{\"product_name\": \"Baju Kedokteran Biru\", \"product_price\": 250000, \"product_stock\": 0}', 0, '2024-12-18 20:53:40'),
(139, 'INSERT', 'orders', 'order_id: 90', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-18 14:54:18\", \"order_status\": \"Pending\"}', 22, '2024-12-18 20:54:18'),
(140, 'UPDATE', 'products', 'product_id: 85', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 5}', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 4}', 0, '2024-12-18 20:56:49'),
(141, 'INSERT', 'orders', 'order_id: 91', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Siti\", \"lastname\": \"Audrey\", \"email\": \"sitiaudrey@gmail.com\", \"contact_no\": \"081286488888\", \"city\": \"KOTA MEDAN\", \"pincode\": \"2199\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-18 14:57:35\", \"order_status\": \"Pending\"}', 22, '2024-12-18 20:57:35'),
(142, 'UPDATE', 'orders', 'order_id: 90', '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-18 14:54:18\", \"order_status\": \"Pending\"}', '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081286488876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-18 14:54:18\", \"order_status\": \"Complete\"}', 22, '2024-12-18 20:59:13'),
(143, 'UPDATE', 'products', 'product_id: 85', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 4}', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 2}', 0, '2024-12-18 21:04:01'),
(144, 'INSERT', 'orders', 'order_id: 92', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Paskal\", \"email\": \"paskalm30@gmail.com\", \"contact_no\": \"081370998486\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-18 15:12:59\", \"order_status\": \"Pending\"}', 22, '2024-12-18 21:12:59'),
(145, 'UPDATE', 'products', 'product_id: 101', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 30}', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 29}', 0, '2024-12-19 00:13:57'),
(146, 'INSERT', 'orders', 'order_id: 93', NULL, '{\"user_id\": 22, \"address\": \"Jln Marelan\", \"firstname\": \"Zalva\", \"lastname\": \"Zahira\", \"email\": \"zalva@gmail.com\", \"contact_no\": \"08123456789\", \"city\": \"Medan\", \"pincode\": \"20211\", \"payment_mode\": \"BCA\", \"order_date\": \"2024-12-18 18:14:41\", \"order_status\": \"Pending\"}', 22, '2024-12-19 00:14:41'),
(147, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 13}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 12}', 0, '2024-12-19 00:15:55'),
(148, 'INSERT', 'orders', 'order_id: 94', NULL, '{\"user_id\": 22, \"address\": \"Jalan Marelan\", \"firstname\": \"Zalva\", \"lastname\": \"Zahira\", \"email\": \"zalva@gmail.com\", \"contact_no\": \"08123456789\", \"city\": \"Medan\", \"pincode\": \"20211\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-18 18:16:30\", \"order_status\": \"Pending\"}', 22, '2024-12-19 00:16:30'),
(149, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 12}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 11}', 0, '2024-12-19 00:20:37'),
(150, 'INSERT', 'orders', 'order_id: 95', NULL, '{\"user_id\": 22, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"customer\", \"lastname\": \"Paskal\", \"email\": \"mayadisilalahi@gmail.com\", \"contact_no\": \"081286488888\", \"city\": \"KOTA MEDAN\", \"pincode\": \"121213\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-18 18:21:26\", \"order_status\": \"Pending\"}', 22, '2024-12-19 00:21:26'),
(151, 'UPDATE', 'menu', 'menu_id: 1', '{\"nama_barang\": \"Celana\", \"harga_barang\": 18000}', '{\"nama_barang\": \"Celana\", \"harga_barang\": 25000}', 23, '2024-12-19 10:07:26'),
(152, 'UPDATE', 'menu', 'menu_id: 2', '{\"nama_barang\": \"Baju\", \"harga_barang\": 16000}', '{\"nama_barang\": \"Baju\", \"harga_barang\": 45000}', 23, '2024-12-19 10:08:00'),
(153, 'UPDATE', 'menu', 'menu_id: 3', '{\"nama_barang\": \"Lainnya\", \"harga_barang\": 20000}', '{\"nama_barang\": \"Lainnya\", \"harga_barang\": 60000}', 23, '2024-12-19 10:08:11'),
(154, 'UPDATE', 'menu', 'menu_id: 1', '{\"nama_barang\": \"Celana\", \"harga_barang\": 25000}', '{\"nama_barang\": \"Celana\", \"harga_barang\": 100000}', 23, '2024-12-19 10:36:04'),
(155, 'UPDATE', 'menu', 'menu_id: 2', '{\"nama_barang\": \"Baju\", \"harga_barang\": 45000}', '{\"nama_barang\": \"Baju\", \"harga_barang\": 100000}', 23, '2024-12-19 10:36:11'),
(156, 'UPDATE', 'menu', 'menu_id: 3', '{\"nama_barang\": \"Lainnya\", \"harga_barang\": 60000}', '{\"nama_barang\": \"Lainnya\", \"harga_barang\": 200000}', 23, '2024-12-19 10:36:17'),
(157, 'DELETE', 'request', 'request_id: 26', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 26}', NULL, 26, '2024-12-19 10:39:27'),
(158, 'DELETE', 'request', 'request_id: 28', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(159, 'DELETE', 'request', 'request_id: 32', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(160, 'DELETE', 'request', 'request_id: 33', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 0, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(161, 'DELETE', 'request', 'request_id: 34', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(162, 'DELETE', 'request', 'request_id: 35', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(163, 'DELETE', 'request', 'request_id: 36', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(164, 'DELETE', 'request', 'request_id: 70', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:27'),
(165, 'DELETE', 'request', 'request_id: 81', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 7, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-19 10:39:27'),
(166, 'DELETE', 'request', 'request_id: 63', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:46'),
(167, 'DELETE', 'request', 'request_id: 64', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:46'),
(168, 'DELETE', 'request', 'request_id: 66', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"S\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:46'),
(169, 'DELETE', 'request', 'request_id: 67', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 7, \"menu_id\": 3, \"user_id\": 22}', NULL, 22, '2024-12-19 10:39:46'),
(170, 'INSERT', 'request', '86', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 8, \"gambar\": \"Screenshot 2023-12-11 213529.png\", \"saran\": \"Lorem Ipsum\", \"status\": \"pending\", \"created_at\": \"2024-12-19 11:18:05\", \"updated_at\": \"2024-12-19 11:18:05\", \"cancel_date\": null, \"menu_id\": 3}', 22, '2024-12-19 11:18:05'),
(171, 'INSERT', 'request', '87', NULL, '{\"nama\": \"Celana\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro24.jpg\", \"saran\": \"Modelnya kek gitu ,tapi koyakin di bagian bahunya\", \"status\": \"pending\", \"created_at\": \"2024-12-19 11:23:18\", \"updated_at\": \"2024-12-19 11:23:18\", \"cancel_date\": null, \"menu_id\": 1}', 22, '2024-12-19 11:23:18'),
(172, 'INSERT', 'request', '88', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 8, \"gambar\": \"pro4.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-19 11:41:58\", \"updated_at\": \"2024-12-19 11:41:58\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-19 11:41:58'),
(173, 'INSERT', 'request', '89', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 8, \"gambar\": \"pro4.jpg\", \"saran\": \"oke\", \"status\": \"pending\", \"created_at\": \"2024-12-19 11:44:28\", \"updated_at\": \"2024-12-19 11:44:28\", \"cancel_date\": null, \"menu_id\": 2}', 22, '2024-12-19 11:44:28'),
(174, 'INSERT', 'request', '97', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"pro5.jpg\", \"saran\": \"Lorem\", \"status\": \"pending\", \"created_at\": \"2024-12-19 12:20:37\", \"updated_at\": \"2024-12-19 12:20:37\", \"cancel_date\": null, \"menu_id\": 3}', 23, '2024-12-19 12:20:37'),
(175, 'INSERT', 'request', '98', NULL, '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 8, \"gambar\": \"\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-19 12:21:02\", \"updated_at\": \"2024-12-19 12:21:02\", \"cancel_date\": null, \"menu_id\": 3}', 23, '2024-12-19 12:21:02'),
(176, 'INSERT', 'request', '99', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"\", \"saran\": \"Ipsum\", \"status\": \"pending\", \"created_at\": \"2024-12-19 12:22:36\", \"updated_at\": \"2024-12-19 12:22:36\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-19 12:22:36'),
(177, 'INSERT', 'request', '100', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 9, \"gambar\": \"\", \"saran\": \"Mantap\", \"status\": \"pending\", \"created_at\": \"2024-12-19 12:25:25\", \"updated_at\": \"2024-12-19 12:25:25\", \"cancel_date\": null, \"menu_id\": 2}', 23, '2024-12-19 12:25:25'),
(178, 'DELETE', 'request', 'request_id: 88', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"L\", \"jumlah\": 8, \"menu_id\": 2, \"user_id\": 22}', NULL, 22, '2024-12-19 12:26:58'),
(179, 'DELETE', 'request', 'request_id: 97', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 9, \"menu_id\": 3, \"user_id\": 23}', NULL, 23, '2024-12-19 12:26:58'),
(180, 'DELETE', 'request', 'request_id: 98', '{\"nama\": \"Lainnya\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 8, \"menu_id\": 3, \"user_id\": 23}', NULL, 23, '2024-12-19 12:26:58'),
(181, 'DELETE', 'request', 'request_id: 99', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"S\", \"jumlah\": 9, \"menu_id\": 2, \"user_id\": 23}', NULL, 23, '2024-12-19 12:26:58'),
(182, 'UPDATE', 'products', 'product_id: 85', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 2}', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 1}', 0, '2024-12-19 13:06:17'),
(183, 'UPDATE', 'products', 'product_id: 85', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 1}', '{\"product_name\": \"Baju Scrub Hijau\", \"product_price\": 250000, \"product_stock\": 0}', 0, '2024-12-19 13:08:34'),
(184, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 11}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 10}', 0, '2024-12-19 13:08:46'),
(185, 'INSERT', 'orders', 'order_id: 96', NULL, '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"Pending\"}', 29, '2024-12-19 13:09:48');
INSERT INTO `logs` (`activity_id`, `action_type`, `table_name`, `affected_data`, `nilai_lama`, `nilai_baru`, `user_id`, `action_date`) VALUES
(186, 'UPDATE', 'orders', 'order_id: 96', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"Pending\"}', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"Complete\"}', 29, '2024-12-19 13:11:54'),
(187, 'UPDATE', 'orders', 'order_id: 96', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"Complete\"}', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"In-Process\"}', 29, '2024-12-19 13:14:06'),
(188, 'UPDATE', 'orders', 'order_id: 96', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"In-Process\"}', '{\"user_id\": 29, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskalirvaldi10@gmail.com\", \"contact_no\": \"0812864888876\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BNI\", \"order_date\": \"2024-12-19 07:09:48\", \"order_status\": \"Complete\"}', 29, '2024-12-19 13:14:30'),
(189, 'INSERT', 'request', '101', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Pendek\", \"jumlah\": 5, \"gambar\": \"pro22.jpg\", \"saran\": \"Koyakin Kanannya\", \"status\": \"pending\", \"created_at\": \"2024-12-19 13:15:44\", \"updated_at\": \"2024-12-19 13:15:44\", \"cancel_date\": null, \"menu_id\": 2}', 29, '2024-12-19 13:15:44'),
(190, 'DELETE', 'request', 'request_id: 101', '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"jumlah\": 0, \"menu_id\": 2, \"user_id\": 29}', NULL, 29, '2024-12-19 13:23:29'),
(191, 'UPDATE', 'products', 'product_id: 88', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 10}', '{\"product_name\": \"Jas Dokter\", \"product_price\": 300000, \"product_stock\": 9}', 0, '2024-12-19 14:52:11'),
(192, 'UPDATE', 'products', 'product_id: 101', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 29}', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 28}', 0, '2024-12-19 14:52:29'),
(193, 'INSERT', 'orders', 'order_id: 97', NULL, '{\"user_id\": 30, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskal@gmail.com\", \"contact_no\": \"081370998686\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-19 08:53:24\", \"order_status\": \"Pending\"}', 30, '2024-12-19 14:53:24'),
(194, 'UPDATE', 'orders', 'order_id: 97', '{\"user_id\": 30, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskal@gmail.com\", \"contact_no\": \"081370998686\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-19 08:53:24\", \"order_status\": \"Pending\"}', '{\"user_id\": 30, \"address\": \"JL. SEI BAHKAPURAN NO. 16-B\", \"firstname\": \"Paskal\", \"lastname\": \"Manik\", \"email\": \"paskal@gmail.com\", \"contact_no\": \"081370998686\", \"city\": \"KOTA MEDAN\", \"pincode\": \"20119\", \"payment_mode\": \"BRI\", \"order_date\": \"2024-12-19 08:53:24\", \"order_status\": \"Complete\"}', 30, '2024-12-19 14:54:41'),
(195, 'INSERT', 'request', '102', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Laki-Laki\", \"ukuran_baju\": \"M\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 7, \"gambar\": \"pro30.jpg\", \"saran\": \"Koyak koyak bang\", \"status\": \"pending\", \"created_at\": \"2024-12-19 14:56:36\", \"updated_at\": \"2024-12-19 14:56:36\", \"cancel_date\": null, \"menu_id\": 2}', 30, '2024-12-19 14:56:36'),
(196, 'INSERT', 'request', '103', NULL, '{\"nama\": \"Baju\", \"jenis_kelamin\": \"Perempuan\", \"ukuran_baju\": \"L\", \"ukuran_lengan\": \"Panjang\", \"jumlah\": 5, \"gambar\": \"pro20.jpg\", \"saran\": \"Oke\", \"status\": \"pending\", \"created_at\": \"2024-12-19 15:02:21\", \"updated_at\": \"2024-12-19 15:02:21\", \"cancel_date\": null, \"menu_id\": 2}', 30, '2024-12-19 15:02:21'),
(197, 'UPDATE', 'products', 'product_id: 101', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 28}', '{\"product_name\": \"Seragam Anak TK\", \"product_price\": 125000, \"product_stock\": 27}', 0, '2024-12-19 15:12:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `harga_barang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`menu_id`, `nama_barang`, `harga_barang`) VALUES
(1, 'Celana', 100000),
(2, 'Baju', 100000),
(3, 'Lainnya', 200000);

--
-- Trigger `menu`
--
DELIMITER $$
CREATE TRIGGER `after_menu_delete` AFTER DELETE ON `menu` FOR EACH ROW BEGIN
    DECLARE logged_user_id INT;
    SET logged_user_id = @user_id;

    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_lama, 
        user_id
    )
    VALUES (
        'DELETE', 
        'menu', 
        CONCAT('menu_id: ', OLD.menu_id), 
        JSON_OBJECT('nama_barang', OLD.nama_barang, 'harga_barang', OLD.harga_barang), 
        logged_user_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_menu_insert` AFTER INSERT ON `menu` FOR EACH ROW BEGIN
    DECLARE logged_user_id INT;
    SET logged_user_id = @user_id;

    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_baru, 
        user_id
    )
    VALUES (
        'INSERT', 
        'menu', 
        CONCAT('menu_id: ', NEW.menu_id), 
        JSON_OBJECT('nama_barang', NEW.nama_barang, 'harga_barang', NEW.harga_barang), 
        logged_user_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_menu_update` AFTER UPDATE ON `menu` FOR EACH ROW BEGIN
    DECLARE logged_user_id INT;
    SET logged_user_id = @user_id;

    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_lama, 
        nilai_baru, 
        user_id
    )
    VALUES (
        'UPDATE', 
        'menu', 
        CONCAT('menu_id: ', OLD.menu_id), 
        JSON_OBJECT('nama_barang', OLD.nama_barang, 'harga_barang', OLD.harga_barang), 
        JSON_OBJECT('nama_barang', NEW.nama_barang, 'harga_barang', NEW.harga_barang), 
        logged_user_id
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `user_id` int(20) NOT NULL,
  `order_id` int(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact_no` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `pincode` varchar(50) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `order_date` varchar(20) NOT NULL,
  `order_status` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`user_id`, `order_id`, `address`, `firstname`, `lastname`, `email`, `contact_no`, `city`, `pincode`, `payment_mode`, `payment_proof`, `order_date`, `order_status`) VALUES
(22, 91, 'JL. SEI BAHKAPURAN NO. 16-B', 'Siti', 'Audrey', 'sitiaudrey@gmail.com', '081286488888', 'KOTA MEDAN', '2199', 'BNI', '', '2024-12-18 14:57:35', 'Pending'),
(22, 90, 'JL. SEI BAHKAPURAN NO. 16-B', 'Paskal', 'Manik', 'paskalm30@gmail.com', '081286488876', 'KOTA MEDAN', '20119', 'BRI', '', '2024-12-18 14:54:18', 'Complete'),
(22, 89, 'JL. SEI BAHKAPURAN NO. 16-B', 'Naurah', 'Alya', 'naurah@gmail.com', '081286488876', 'KOTA MEDAN', '121213', 'BRI', '', '2024-12-17 14:31:04', 'Pending'),
(22, 92, 'JL. SEI BAHKAPURAN NO. 16-B', 'Paskal', 'Paskal', 'paskalm30@gmail.com', '081370998486', 'KOTA MEDAN', '20119', 'BNI', 'img/payment_proofs/proof_6762d86be05078.61648022.jpg', '2024-12-18 15:12:59', 'Pending'),
(22, 95, 'JL. SEI BAHKAPURAN NO. 16-B', 'customer', 'Paskal', 'mayadisilalahi@gmail.com', '081286488888', 'KOTA MEDAN', '121213', 'BRI', 'img/payment_proofs/proof_67630496da6969.52236260.jpg', '2024-12-18 18:21:26', 'Pending'),
(30, 97, 'JL. SEI BAHKAPURAN NO. 16-B', 'Paskal', 'Manik', 'paskal@gmail.com', '081370998686', 'KOTA MEDAN', '20119', 'BRI', 'img/payment_proofs/proof_6763d0f44fe2d4.96154033.jpg', '2024-12-19 08:53:24', 'Complete');

--
-- Trigger `orders`
--
DELIMITER $$
CREATE TRIGGER `after_orders_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_baru, 
        user_id
    )
    VALUES (
        'INSERT', 
        'orders', 
        CONCAT('order_id: ', NEW.order_id), 
        JSON_OBJECT(
            'user_id', NEW.user_id, 
            'address', NEW.address, 
            'firstname', NEW.firstname, 
            'lastname', NEW.lastname, 
            'email', NEW.email, 
            'contact_no', NEW.contact_no, 
            'city', NEW.city, 
            'pincode', NEW.pincode, 
            'payment_mode', NEW.payment_mode, 
            'order_date', NEW.order_date, 
            'order_status', NEW.order_status
        ), 
        NEW.user_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_orders_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_lama, 
        nilai_baru, 
        user_id
    )
    VALUES (
        'UPDATE', 
        'orders', 
        CONCAT('order_id: ', OLD.order_id), 
        JSON_OBJECT(
            'user_id', OLD.user_id, 
            'address', OLD.address, 
            'firstname', OLD.firstname, 
            'lastname', OLD.lastname, 
            'email', OLD.email, 
            'contact_no', OLD.contact_no, 
            'city', OLD.city, 
            'pincode', OLD.pincode, 
            'payment_mode', OLD.payment_mode, 
            'order_date', OLD.order_date, 
            'order_status', OLD.order_status
        ), 
        JSON_OBJECT(
            'user_id', NEW.user_id, 
            'address', NEW.address, 
            'firstname', NEW.firstname, 
            'lastname', NEW.lastname, 
            'email', NEW.email, 
            'contact_no', NEW.contact_no, 
            'city', NEW.city, 
            'pincode', NEW.pincode, 
            'payment_mode', NEW.payment_mode, 
            'order_date', NEW.order_date, 
            'order_status', NEW.order_status
        ), 
        NEW.user_id
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(50) DEFAULT NULL,
  `product_id` int(5) DEFAULT NULL,
  `product_price` int(10) DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_price`, `quantity`) VALUES
(37, 89, 88, 300000, 2),
(38, 90, 86, 250000, 2),
(39, 91, 85, 250000, 1),
(40, 92, 85, 250000, 2),
(43, 95, 88, 300000, 1),
(46, 97, 88, 300000, 3),
(47, 97, 101, 125000, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(5) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_slider` varchar(20) NOT NULL,
  `product_price` int(5) NOT NULL,
  `product_stock` int(5) NOT NULL,
  `product_img` varchar(600) NOT NULL,
  `product_preview` varchar(1000) NOT NULL,
  `product_description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `product_name`, `product_slider`, `product_price`, `product_stock`, `product_img`, `product_preview`, `product_description`) VALUES
(85, 'Baju Scrub Hijau', 'Product', 250000, 0, 'product_img/8bcd106e0beec841e3f0545a0896612bpro39.jpg', 'a:4:{i:0;s:65:\"product_img/preview_img/8bcd106e0beec841e3f0545a0896612bpro39.jpg\";i:1;s:65:\"product_img/preview_img/8bcd106e0beec841e3f0545a0896612bpro38.jpg\";i:2;s:65:\"product_img/preview_img/8bcd106e0beec841e3f0545a0896612bpro39.jpg\";i:3;s:65:\"product_img/preview_img/8bcd106e0beec841e3f0545a0896612bpro38.jpg\";}', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Error possimus hic laudantium ipsum dicta, aut officia voluptas praesentium earum omnis consectetur nostrum ea, veritatis voluptate? Vero laboriosam soluta recusandae nihil!                                                                                                                                            '),
(86, 'Baju Kedokteran Biru', 'Product', 250000, 0, 'product_img/f6c7fee95c1b6c1a279efb3145a39920pro40.jpg', 'a:4:{i:0;s:65:\"product_img/preview_img/f6c7fee95c1b6c1a279efb3145a39920pro40.jpg\";i:1;s:65:\"product_img/preview_img/f6c7fee95c1b6c1a279efb3145a39920pro37.jpg\";i:2;s:65:\"product_img/preview_img/f6c7fee95c1b6c1a279efb3145a39920pro40.jpg\";i:3;s:65:\"product_img/preview_img/f6c7fee95c1b6c1a279efb3145a39920pro37.jpg\";}', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat saepe expedita consequuntur maiores! Odio perferendis dignissimos facilis, nisi velit deserunt sint numquam provident nemo illo sed! Fugiat perferendis fugit excepturi!'),
(88, 'Jas Dokter', 'Product', 300000, 9, 'product_img/5bf8d8679039a4ee4aacab11e23aa9eapro35.jpg', 'a:4:{i:0;s:65:\"product_img/preview_img/5bf8d8679039a4ee4aacab11e23aa9eapro35.jpg\";i:1;s:65:\"product_img/preview_img/5bf8d8679039a4ee4aacab11e23aa9eapro36.jpg\";i:2;s:65:\"product_img/preview_img/5bf8d8679039a4ee4aacab11e23aa9eapro35.jpg\";i:3;s:65:\"product_img/preview_img/5bf8d8679039a4ee4aacab11e23aa9eapro36.jpg\";}', 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore tempora dicta ut dolorum voluptate iusto quia nobis reiciendis voluptas aliquid? Inventore recusandae quam dolore accusantium? Repellendus, iure culpa. Aspernatur, error!                                        '),
(101, 'Seragam Anak TK', 'Product', 125000, 27, 'product_img/b718d165297cd7758ec51355db4d552cpro42.jpg', 'a:4:{i:0;s:65:\"product_img/preview_img/b718d165297cd7758ec51355db4d552cpro42.jpg\";i:1;s:65:\"product_img/preview_img/b718d165297cd7758ec51355db4d552cpro41.jpg\";i:2;s:56:\"product_img/preview_img/b718d165297cd7758ec51355db4d552c\";i:3;s:56:\"product_img/preview_img/b718d165297cd7758ec51355db4d552c\";}', 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Optio, pariatur deserunt vero, quasi sequi nobis eveniet ipsam id eius placeat delectus porro sapiente facere aperiam excepturi ab, non recusandae? Repellendus.                    ');

--
-- Trigger `products`
--
DELIMITER $$
CREATE TRIGGER `after_products_delete` AFTER DELETE ON `products` FOR EACH ROW BEGIN
    INSERT INTO logs (
        action_type, table_name, affected_data, nilai_lama, nilai_baru, user_id, action_date
    )
    VALUES (
        'DELETE',
        'products',
        CONCAT('id: ', OLD.id),
        JSON_OBJECT(
            'product_name', OLD.product_name,
            'product_slider', OLD.product_slider,
            'product_price', OLD.product_price,
            'product_stock', OLD.product_stock,
            'product_img', OLD.product_img,
            'product_preview', OLD.product_preview,
            'product_description', OLD.product_description
        ),
        NULL,
        @user_id, -- Get user_id from the session variable passed from PHP
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_products_insert` AFTER INSERT ON `products` FOR EACH ROW BEGIN
    -- Insert log ke tabel logs
    INSERT INTO logs (
        table_name, action_type, affected_data, user_id, nilai_lama, nilai_baru, action_date
    )
    VALUES (
        'products',                  
        'INSERT',                     
        NEW.id,                       
        @user_id,                     
        NULL,                         
        JSON_OBJECT(                  
            'product_name', NEW.product_name,
            'product_slider', NEW.product_slider,
            'product_price', NEW.product_price,
            'product_stock', NEW.product_stock,
            'product_img', NEW.product_img,
            'product_preview', NEW.product_preview,
            'product_description', NEW.product_description
        ),
        NOW()                         
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_products_update` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_lama, 
        nilai_baru, 
        user_id
    )
    VALUES (
        'UPDATE', 
        'products', 
        CONCAT('product_id: ', OLD.id), 
        JSON_OBJECT(
            'product_name', OLD.product_name, 
            'product_price', OLD.product_price, 
            'product_stock', OLD.product_stock
        ), 
        JSON_OBJECT(
            'product_name', NEW.product_name, 
            'product_price', NEW.product_price, 
            'product_stock', NEW.product_stock
        ), 
        'USER_ID_PLACEHOLDER'
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `report_id` int(40) NOT NULL,
  `report_date` varchar(30) NOT NULL,
  `order_id` int(49) NOT NULL,
  `user_id` int(40) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` varchar(400) NOT NULL,
  `status` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`report_id`, `report_date`, `order_id`, `user_id`, `subject`, `message`, `status`) VALUES
(11, '10-12-24', 84, 22, 'Cancel Order', 'Salah Pesan', 'In Process');

-- --------------------------------------------------------

--
-- Struktur dari tabel `request`
--

CREATE TABLE `request` (
  `request_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') NOT NULL,
  `ukuran_baju` varchar(10) NOT NULL,
  `ukuran_lengan` varchar(10) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `saran` varchar(255) NOT NULL,
  `status` enum('pending','in_progress','completed','approved','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL,
  `cancel_date` datetime DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `request`
--

INSERT INTO `request` (`request_id`, `nama`, `jenis_kelamin`, `ukuran_baju`, `ukuran_lengan`, `jumlah`, `gambar`, `bukti_pembayaran`, `saran`, `status`, `created_at`, `updated_at`, `user_id`, `cancel_date`, `menu_id`) VALUES
(37, 'Lainnya', 'Perempuan', 'S', 'Pendek', 0, 'pro27.jpg', 'pro10.jpg', 'oke', 'completed', '2024-12-17 06:50:24', '2024-12-19 05:28:07', 22, NULL, 3),
(68, 'Lainnya', 'Laki-Laki', 'S', 'Panjang', 0, 'pro5.jpg', 'pro10.jpg', 'oke', 'completed', '2024-12-18 08:57:43', '2024-12-19 05:28:03', 22, NULL, 3),
(69, 'Lainnya', 'Laki-Laki', 'S', 'Panjang', 0, 'pro5.jpg', 'pro10.jpg', 'oke', 'completed', '2024-12-18 09:00:58', '2024-12-19 05:27:17', 22, NULL, 3),
(86, 'Lainnya', 'Laki-Laki', 'M', 'Panjang', 8, 'Screenshot 2023-12-11 213529.png', NULL, 'Lorem Ipsum', 'pending', '2024-12-19 04:18:05', '2024-12-19 04:18:05', 22, NULL, 3),
(87, 'Celana', 'Laki-Laki', 'M', 'Pendek', 9, 'pro24.jpg', NULL, 'Modelnya kek gitu ,tapi koyakin di bagian bahunya', 'cancelled', '2024-12-19 04:23:18', '2024-12-19 05:27:31', 22, '2024-12-19 06:27:31', 1),
(89, 'Baju', 'Laki-Laki', 'L', 'Pendek', 8, 'pro4.jpg', 'pro10.jpg', 'oke', 'pending', '2024-12-19 04:44:28', '2024-12-19 04:44:28', 22, NULL, 2),
(100, 'Baju', 'Laki-Laki', 'S', 'Pendek', 9, '', 'pro6.jpg', 'Mantap', 'pending', '2024-12-19 05:25:25', '2024-12-19 05:25:25', 23, NULL, 2),
(102, 'Baju', 'Laki-Laki', 'M', 'Panjang', 0, 'pro30.jpg', 'WhatsApp Image 2024-12-19 at 12.33.45_80c60aa0.jpg', 'Koyak koyak bang', 'completed', '2024-12-19 07:56:36', '2024-12-19 07:59:49', 30, NULL, 2),
(103, 'Baju', 'Perempuan', 'L', 'Panjang', 5, 'pro20.jpg', 'pro6.jpg', 'Oke', 'pending', '2024-12-19 08:02:21', '2024-12-19 08:02:21', 30, NULL, 2);

--
-- Trigger `request`
--
DELIMITER $$
CREATE TRIGGER `after_request_delete` AFTER DELETE ON `request` FOR EACH ROW BEGIN
    INSERT INTO logs (
        action_type, 
        table_name, 
        affected_data, 
        nilai_lama, 
        user_id
    )
    VALUES (
        'DELETE', 
        'request', 
        CONCAT('request_id: ', OLD.request_id), 
        JSON_OBJECT(
            'nama', OLD.nama, 
            'jenis_kelamin', OLD.jenis_kelamin, 
            'ukuran_baju', OLD.ukuran_baju, 
            'jumlah', OLD.jumlah, 
            'menu_id', OLD.menu_id, 
            'user_id', OLD.user_id
        ), 
        OLD.user_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_request_insert` AFTER INSERT ON `request` FOR EACH ROW BEGIN
    INSERT INTO logs (
        table_name, action_type, affected_data, user_id, nilai_lama, nilai_baru, action_date
    )
    VALUES (
        'request', 'INSERT', NEW.request_id, NEW.user_id, NULL,
        JSON_OBJECT(
            'nama', NEW.nama,
            'jenis_kelamin', NEW.jenis_kelamin,
            'ukuran_baju', NEW.ukuran_baju,
            'ukuran_lengan', NEW.ukuran_lengan,
            'jumlah', NEW.jumlah,
            'gambar', NEW.gambar,
            'saran', NEW.saran,
            'status', NEW.status,
            'created_at', NEW.created_at,
            'updated_at', NEW.updated_at,
            'cancel_date', NEW.cancel_date,
            'menu_id', NEW.menu_id
        ),
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_log`
--

CREATE TABLE `request_log` (
  `log_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `bukti_selesai` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jumlah_diambil` int(11) NOT NULL,
  `waktu_diambil` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('in_progress','completed','waiting_admin') DEFAULT 'in_progress'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `request_log`
--

INSERT INTO `request_log` (`log_id`, `request_id`, `bukti_selesai`, `user_id`, `jumlah_diambil`, `waktu_diambil`, `created_at`, `status`) VALUES
(51, 69, '1734516620_pro5.jpg', 24, 6, '2024-12-18 11:10:13', '2024-12-18 10:10:13', 'completed'),
(53, 68, '1734516708_pro5.jpg', 24, 6, '2024-12-18 11:11:41', '2024-12-18 10:11:41', 'completed'),
(54, 68, '1734516765_pro5.jpg', 25, 3, '2024-12-18 11:12:38', '2024-12-18 10:12:38', 'completed'),
(57, 102, '1734595087_pro30.jpg', 24, 3, '2024-12-19 08:57:47', '2024-12-19 07:57:47', 'completed'),
(58, 102, '1734595155_pro30.jpg', 24, 4, '2024-12-19 08:59:08', '2024-12-19 07:59:08', 'completed');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `role` enum('customer','admin','employee') NOT NULL DEFAULT 'customer',
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `contact_no`, `role`, `password`, `is_verified`, `verification_code`, `created_at`, `updated_at`) VALUES
(22, 'Paskal', 'Manik', 'paskalm30@gmail.com', '081370998486', 'customer', '$2y$10$ILG3YbZYR1EgyWn6tstQXejrhoY2FPZbakwwxpN3E0yUjp9nJVye.', 1, '', '2024-12-02 16:43:45', '2024-12-15 07:46:48'),
(23, 'Admin', 'Asli', 'admin@gmail.com', '081370998486', 'admin', '$2y$10$e9e816QJPLzHVPx0yAKmmOeQBmIZ48gvagK7FOFmcuMHl5DB6Hmaq', 1, '', '2024-12-02 17:04:19', '2024-12-02 17:04:42'),
(24, 'employee', 'Asli', 'employee@gmail.com', '081286488888', 'employee', '$2y$10$jhjmeNbpCggYQjRDBafgHuUbUd.zvoe1tV4S.i3WFZ0CGWRIlIkjm', 1, '', '2024-12-02 17:23:54', '2024-12-02 17:24:14'),
(25, 'Employee2', 'Asli', 'employee2@gmail.com', '081286488888', 'employee', '$2y$10$Wn93vYLPP4LcwpVnlXs7YeeA9S228qbkiHnzPV80cdI0XDxW7Osb2', 1, '', '2024-12-03 17:27:00', '2024-12-19 08:01:32'),
(26, 'customer', '1', 'customer@gmail.com', '0812864888876', 'customer', '$2y$10$f9p/uTpLrsPWv2oNcpu2NOPEgy6Hg/k9GUI10jAMbEbTV5I5GxKxa', 1, '', '2024-12-10 05:38:22', '2024-12-19 06:20:48'),
(29, 'Paskal', 'Manik', 'paskalirvaldi10@gmail.com', '085270000000', 'customer', '$2y$10$kCOctJ/jxP3njZD2kAOPxOo8VYMBjgnD3jBRw5n85lvfcBuV70WGO', 1, '', '2024-12-19 06:07:53', '2024-12-19 06:08:13'),
(30, 'Paskal', 'Manik', 'paskal@gmail.com', '081370998686', 'customer', '$2y$10$U/QcsKDShifNxwuNB9hh2.QGBYvjEprJbeIH.tXHXJhQSXpmmV/uq', 1, '', '2024-12-19 07:51:03', '2024-12-19 07:51:41');

-- --------------------------------------------------------

--
-- Struktur untuk view `cart_view`
--
DROP TABLE IF EXISTS `cart_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`` SQL SECURITY DEFINER VIEW `cart_view`  AS SELECT `c`.`id` AS `id`, `c`.`user_id` AS `user_id`, `p`.`product_name` AS `product_name`, `c`.`quantity` AS `quantity`, `p`.`product_price` AS `product_price`, `p`.`product_img` AS `product_img`, `c`.`quantity`* `p`.`product_price` AS `TotalHarga` FROM (`cart` `c` join `products` `p` on(`c`.`id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `full_order_view`
--
DROP TABLE IF EXISTS `full_order_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`` SQL SECURITY DEFINER VIEW `full_order_view`  AS SELECT `o`.`order_id` AS `order_id`, `o`.`order_status` AS `order_status`, `o`.`order_date` AS `order_date`, `o`.`firstname` AS `firstname`, `o`.`lastname` AS `lastname`, `o`.`address` AS `address`, `o`.`city` AS `city`, `o`.`pincode` AS `pincode`, `o`.`payment_mode` AS `payment_mode`, `u`.`user_id` AS `user_id`, sum(`HitungTotalHarga`(`p`.`product_price`,`oi`.`quantity`)) AS `TotalHarga` FROM (((`orders` `o` join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) join `products` `p` on(`oi`.`product_id` = `p`.`id`)) join `users` `u` on(`o`.`user_id` = `u`.`user_id`)) GROUP BY `o`.`order_id`, `o`.`order_status`, `o`.`order_date`, `o`.`firstname`, `o`.`lastname`, `o`.`address`, `o`.`city`, `o`.`pincode`, `o`.`payment_mode`, `u`.`user_id` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `gaji_view`
--
DROP TABLE IF EXISTS `gaji_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`` SQL SECURITY DEFINER VIEW `gaji_view`  AS SELECT 'Request' AS `source`, sum(`rl`.`jumlah_diambil` * `m`.`harga_barang`) AS `total_pendapatan`, sum(`rl`.`jumlah_diambil` * `m`.`harga_barang`) * 0.6 AS `pendapatan_pemilik` FROM (((`request_log` `rl` join `users` `u` on(`rl`.`user_id` = `u`.`user_id`)) join `request` `r` on(`rl`.`request_id` = `r`.`request_id`)) join `menu` `m` on(`r`.`menu_id` = `m`.`menu_id`)) WHERE `u`.`role` = 'employee' AND `rl`.`status` = 'completed' GROUP BY 'Request'union all select 'Order' AS `source`,sum(`oi`.`product_price` * `oi`.`quantity`) AS `total_pendapatan`,sum(`oi`.`product_price` * `oi`.`quantity`) * 0.6 AS `pendapatan_pemilik` from (`orders` `o` join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) where `o`.`order_status` = 'complete' group by 'Order'  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id` (`id`);

--
-- Indeks untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indeks untuk tabel `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `request_log`
--
ALTER TABLE `request_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(40) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `request`
--
ALTER TABLE `request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT untuk tabel `request_log`
--
ALTER TABLE `request_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `request_log`
--
ALTER TABLE `request_log`
  ADD CONSTRAINT `request_log_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`),
  ADD CONSTRAINT `request_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
