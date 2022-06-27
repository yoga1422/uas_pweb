-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jun 2022 pada 01.18
-- Versi server: 10.4.22-MariaDB
-- Versi PHP: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uas_pweb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer`
--

CREATE TABLE `customer` (
  `id_customer` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `nomor_hp` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(25) DEFAULT NULL,
  `teknisi` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `tanggal_service` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `customer`
--

INSERT INTO `customer` (`id_customer`, `nama_lengkap`, `alamat`, `nomor_hp`, `tanggal_lahir`, `jenis_kelamin`, `teknisi`, `service`, `tanggal_service`, `created_at`) VALUES
('102034', 'yogapangestu', 'jl.kakap', '089273845', '2002-02-14', '1', '2', '2', '2022-06-06', '2022-06-27 15:42:00'),
('14020208', 'Budi Setiawan', 'jl. tuanku tambusai', '2147483647', '2000-09-09', '1', '2', '3', '2022-08-15', '2022-06-26 15:20:31'),
('14142308', 'Wahyudi', 'jl. marpoyan', '2147483647', '1996-04-26', '1', '2', '2', '2022-07-20', '2022-06-26 15:20:31'),
('14230208', 'Yoga Pangestu', 'jl. kakap', '2147483647', '2002-02-04', '1', '1', '1', '2022-02-02', '2022-06-26 15:20:31'),
('2080208', 'Nuradilla Agustia', 'jl. garuda sakti', '2147483647', '2002-03-09', '2', '3', '3', '2022-05-23', '2022-06-26 15:20:31'),
('23140208', 'Febrian Akmaresta', 'jl. rimbo panjang', '2147483647', '2002-05-03', '1', '2', '2', '2022-03-10', '2022-06-26 15:20:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_kelamin`
--

CREATE TABLE `jenis_kelamin` (
  `id` int(11) NOT NULL,
  `jenis` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `jenis_kelamin`
--

INSERT INTO `jenis_kelamin` (`id`, `jenis`, `created_at`) VALUES
(1, 'Laki-Laki', '0000-00-00 00:00:00'),
(2, 'Perempuan', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service`
--

CREATE TABLE `service` (
  `kode` int(11) NOT NULL,
  `nama_service` varchar(255) DEFAULT NULL,
  `garansi` varchar(255) DEFAULT NULL,
  `harga_service` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `service`
--

INSERT INTO `service` (`kode`, `nama_service`, `garansi`, `harga_service`, `keterangan`, `supplier`, `created_at`) VALUES
(1, 'Full Service', '3 bulan', 'Rp.70.000,00', 'full service garansi 3 bulan', '142302', '2022-06-26 14:49:44'),
(2, 'Ganti Oli', '1 bulan', 'Rp.30.000,00', '-', '231408', '2022-06-26 14:54:24'),
(3, 'Service Rutin', '2 bulan', 'Rp.50.000,00', '-', '231408', '2022-06-27 19:52:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id` varchar(255) NOT NULL,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id`, `nama_supplier`, `created_at`) VALUES
('142302', 'ASTRA MOTOR', '2022-06-26 14:51:42'),
('231408', 'YAMAHA', '2022-06-26 14:51:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `teknisi`
--

CREATE TABLE `teknisi` (
  `id_teknisi` int(11) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `nomor_hp` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `lama_bekerja` varchar(255) DEFAULT NULL,
  `jenis_kelamin` varchar(25) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `teknisi`
--

INSERT INTO `teknisi` (`id_teknisi`, `nama_lengkap`, `alamat`, `nomor_hp`, `tanggal_lahir`, `lama_bekerja`, `jenis_kelamin`, `created_at`) VALUES
(1, 'jamal', 'jl. setia budi', '2147483647', '1990-01-01', '6 tahun', '1', '2022-06-26 15:06:16'),
(2, 'udin', 'jl. HR.soebrantas', '2147483647', '1993-01-05', '4 tahun', '1', '2022-06-26 15:06:16'),
(3, 'karin', 'jl. harapan raya', '2147483647', '1998-05-09', '1 tahun', '2', '2022-06-26 15:06:16'),
(4, 'samsul', 'jl. sudirman', '2147483647', '1987-06-25', '10 tahun', '1', '2022-06-26 15:06:16'),
(5, 'jafar', 'jl. kaharudin', '2147483647', '1999-01-15', '5 bulan', '1', '2022-06-26 15:06:16'),
(12, 'lukman ahmadi', 'jl. marpoyan', '129313', '1992-08-17', '5 tahun', '1', '2022-06-28 01:09:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indeks untuk tabel `jenis_kelamin`
--
ALTER TABLE `jenis_kelamin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`kode`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `teknisi`
--
ALTER TABLE `teknisi`
  ADD PRIMARY KEY (`id_teknisi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jenis_kelamin`
--
ALTER TABLE `jenis_kelamin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `service`
--
ALTER TABLE `service`
  MODIFY `kode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `teknisi`
--
ALTER TABLE `teknisi`
  MODIFY `id_teknisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
