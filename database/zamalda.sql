-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jun 03, 2025 at 02:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zamalda`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `idBarang` int NOT NULL,
  `kodeBarang` varchar(20) NOT NULL,
  `kodeKategori` varchar(20) NOT NULL,
  `namaBarang` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`idBarang`, `kodeBarang`, `kodeKategori`, `namaBarang`, `deskripsi`) VALUES
(1, 'br001', 'kat001', 'korean ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \r\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \r\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n'),
(2, ' br002', 'kat002', 'Jeans lucu gemoy', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \r\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \r\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n'),
(3, 'br003', 'kat003', 'Hoodie Hitam ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \r\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \r\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n'),
(4, 'br004', 'kat003', 'Kemeja Kotak Lengan Panjang', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \r\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \r\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n'),
(5, 'br005', 'kat004', 'Celana Panjang Pria Hitam', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \r\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \r\nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n'),
(79, 'br007', 'kat003', 'Jaket Walk NCT127', 'Jaket Walk adalah merch dari NCT 127 dalam aalbumnya yang bernama walk'),
(80, 'br008', 'kat001', 'cardigan', 'cardingan korean style'),
(83, 'br009', 'kat001', 'cardigan korean', 'cardingan korean style');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail_transaksi` int NOT NULL,
  `kodeTransaksi` varchar(20) NOT NULL,
  `kodeBarang` varchar(20) NOT NULL,
  `tglTransaksi` timestamp NOT NULL,
  `status` enum('belum dibayar','dikemas','dikirim') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail_transaksi`, `kodeTransaksi`, `kodeBarang`, `tglTransaksi`, `status`) VALUES
(1, 'tr001', 'br001', '2025-05-06 20:01:04', 'belum dibayar');

-- --------------------------------------------------------

--
-- Table structure for table `gambarutama`
--

CREATE TABLE `gambarutama` (
  `idGambar` int NOT NULL,
  `kodeBarang` varchar(20) NOT NULL,
  `gambarUtama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gambarutama`
--

INSERT INTO `gambarutama` (`idGambar`, `kodeBarang`, `gambarUtama`) VALUES
(1, 'br001', 'TONIQUE.JPG'),
(2, ' br002', 'pinkJeans.JPG'),
(3, 'br003', 'blackHooedie.JPG'),
(4, 'br004', 'kemejaKotak.JPG'),
(5, 'br005', 'shortPantsBlack.JPG'),
(129, 'br007', 'walk-member.jpeg'),
(130, 'br007', 'walk-depan.jpeg'),
(131, 'br007', 'walk-belakang.jpeg'),
(132, 'br008', '683cd55e98512.jpeg'),
(135, 'br009', '683cdbad6da24.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `gambarvarian`
--

CREATE TABLE `gambarvarian` (
  `idGambarVarian` int NOT NULL,
  `kodeBarang` varchar(20) NOT NULL,
  `gambarvarian` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gambarvarian`
--

INSERT INTO `gambarvarian` (`idGambarVarian`, `kodeBarang`, `gambarvarian`) VALUES
(1, 'br001', 'TONIQUE.JPG'),
(2, ' br002', 'pinkJeans.JPG'),
(3, 'br003', 'blackHooedie.JPG'),
(4, 'br004', 'kemejaKotak.JPG'),
(5, 'br005', 'shortPantsBlack.JPG'),
(16, 'br007', 'walk-hitam.jpeg'),
(17, 'br007', 'walk-hitamputih.jpeg'),
(18, 'br008', '683cd55e996c6.jpeg'),
(21, 'br009', '683cdbad6e62b.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `kategoribarang`
--

CREATE TABLE `kategoribarang` (
  `idKategori` int NOT NULL,
  `kodeKategori` varchar(20) NOT NULL,
  `namaKategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategoribarang`
--

INSERT INTO `kategoribarang` (`idKategori`, `kodeKategori`, `namaKategori`) VALUES
(1, 'kat001', 'wanita - atasan'),
(2, 'kat002', 'wanita - bawahan'),
(3, 'kat003', 'pria - atasan'),
(4, 'kat004', 'pria - bawahan'),
(6, 'kat005', 'anak - atasan');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `idPelanggan` int NOT NULL,
  `kodePelanggan` varchar(10) NOT NULL,
  `namaPelanggan` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `noTelp` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `alamat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`idPelanggan`, `kodePelanggan`, `namaPelanggan`, `foto`, `noTelp`, `email`, `alamat`) VALUES
(1, 'plg001', 'Mark', 'mark.JPEG', '098765312456', 'mark@gmail.com', 'Jl. Kwangya 1000'),
(2, 'plg002', 'Haechan', 'foto_default.PNG', '081234567890', 'haechan@email.com', 'Seoul 101'),
(3, 'plg003', 'Johnny', 'foto_default.png', '09876531245667', 'jo@email.com', 'nct nation');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `idPengguna` int NOT NULL,
  `kodePengguna` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `level` varchar(20) NOT NULL,
  `status` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`idPengguna`, `kodePengguna`, `username`, `password`, `level`, `status`) VALUES
(1, 'pnjl001', 'Lee Jeno', '123', 'penjual', '1'),
(2, 'plg001', 'Mark', '123', 'Pelanggan', '1'),
(3, 'plg002', 'Haechan', '123', 'Pelanggan', '1'),
(4, 'plg003', 'Johnny', '123', 'Pelanggan', '1');

-- --------------------------------------------------------

--
-- Table structure for table `penjual`
--

CREATE TABLE `penjual` (
  `idPenjual` int NOT NULL,
  `kodePenjual` varchar(20) NOT NULL,
  `namaPenjual` varchar(50) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `noTelp` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penjual`
--

INSERT INTO `penjual` (`idPenjual`, `kodePenjual`, `namaPenjual`, `foto`, `alamat`, `noTelp`) VALUES
(1, 'pnjl001', 'Lee Jeno', 'jeno.png', 'Jl. planet101', '0895228766');

-- --------------------------------------------------------

--
-- Table structure for table `profil_aplikasi`
--

CREATE TABLE `profil_aplikasi` (
  `id` int NOT NULL,
  `nama_aplikasi` varchar(30) NOT NULL,
  `nama_pimpinan` varchar(100) NOT NULL,
  `alamat` varchar(30) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `website` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `profil_aplikasi`
--

INSERT INTO `profil_aplikasi` (`id`, `nama_aplikasi`, `nama_pimpinan`, `alamat`, `no_telp`, `website`, `logo`) VALUES
(1, 'zamelda', 'Lee Soo Man', 'Jl. Kwangya 101', '098765432', 'www.zamelda-shop.com', 'ADS.png');

-- --------------------------------------------------------

--
-- Table structure for table `tampilanawal`
--

CREATE TABLE `tampilanawal` (
  `idTampilanAwal` int NOT NULL,
  `gambarAwal` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tampilanawal`
--

INSERT INTO `tampilanawal` (`idTampilanAwal`, `gambarAwal`) VALUES
(1, 'blackHooedieee.JPG'),
(2, 'kemejaKotakkkk.JPG'),
(3, 'pinkJeanssss.JPG'),
(4, 'TONIQUEEEE.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `idTransaksi` int NOT NULL,
  `kodeTransaksi` varchar(10) NOT NULL,
  `kodePelanggan` varchar(10) NOT NULL,
  `tanggal` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`idTransaksi`, `kodeTransaksi`, `kodePelanggan`, `tanggal`) VALUES
(1, 'tr001', 'plg001', '2025-05-06 19:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `varianbarang`
--

CREATE TABLE `varianbarang` (
  `idVarian` int NOT NULL,
  `kodeBarang` varchar(20) NOT NULL,
  `idGambarVarian` int NOT NULL,
  `typeVarian` varchar(100) NOT NULL,
  `size` varchar(20) NOT NULL,
  `harga` decimal(10,0) NOT NULL,
  `stok` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `varianbarang`
--

INSERT INTO `varianbarang` (`idVarian`, `kodeBarang`, `idGambarVarian`, `typeVarian`, `size`, `harga`, `stok`) VALUES
(1, 'br001', 1, 'pink', 'L', '200000', 12),
(2, ' br002', 2, 'pink', 'XL', '120000', 10),
(3, 'br003', 3, 'hitam', 'L', '750000', 5),
(4, 'br004', 4, 'lengan panjang', 'L', '56000', 120),
(5, 'br005', 5, 'celana hitam', 'L', '299999', 20),
(26, 'br008', 18, 'Hitam', 'L', '90000', 20),
(50, 'br007', 16, 'Hitam', 'L', '127000', 20),
(51, 'br007', 16, 'Hitam', 'M', '127500', 10),
(52, 'br007', 17, 'Hitam Putih', 'L', '100000', 10),
(53, 'br009', 21, 'Hitam Putih', 'L', '200000', 10),
(54, 'br009', 21, 'Hitam Putih', 'M', '30000', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idBarang`),
  ADD UNIQUE KEY `kodeBarang` (`kodeBarang`),
  ADD KEY `kodeKategori` (`kodeKategori`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail_transaksi`),
  ADD KEY `kodeBarang` (`kodeBarang`),
  ADD KEY `detail_transaksi_ibfk_2` (`kodeTransaksi`);

--
-- Indexes for table `gambarutama`
--
ALTER TABLE `gambarutama`
  ADD PRIMARY KEY (`idGambar`),
  ADD KEY `kodeBarang` (`kodeBarang`);

--
-- Indexes for table `gambarvarian`
--
ALTER TABLE `gambarvarian`
  ADD PRIMARY KEY (`idGambarVarian`),
  ADD KEY `kodeBarang` (`kodeBarang`);

--
-- Indexes for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  ADD PRIMARY KEY (`idKategori`),
  ADD UNIQUE KEY `kodeKategori` (`kodeKategori`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`idPelanggan`),
  ADD UNIQUE KEY `kodePelanggan` (`kodePelanggan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`idPengguna`),
  ADD UNIQUE KEY `kodePengguna` (`kodePengguna`);

--
-- Indexes for table `penjual`
--
ALTER TABLE `penjual`
  ADD PRIMARY KEY (`idPenjual`),
  ADD UNIQUE KEY `kodePenjual` (`kodePenjual`);

--
-- Indexes for table `profil_aplikasi`
--
ALTER TABLE `profil_aplikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tampilanawal`
--
ALTER TABLE `tampilanawal`
  ADD PRIMARY KEY (`idTampilanAwal`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`idTransaksi`),
  ADD UNIQUE KEY `kodeTransaksi` (`kodeTransaksi`),
  ADD KEY `kodePelanggan` (`kodePelanggan`);

--
-- Indexes for table `varianbarang`
--
ALTER TABLE `varianbarang`
  ADD PRIMARY KEY (`idVarian`),
  ADD KEY `kodeBarang` (`kodeBarang`),
  ADD KEY `idGambarVarian` (`idGambarVarian`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `idBarang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gambarutama`
--
ALTER TABLE `gambarutama`
  MODIFY `idGambar` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `gambarvarian`
--
ALTER TABLE `gambarvarian`
  MODIFY `idGambarVarian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  MODIFY `idKategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `idPelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `idPengguna` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `penjual`
--
ALTER TABLE `penjual`
  MODIFY `idPenjual` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `profil_aplikasi`
--
ALTER TABLE `profil_aplikasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tampilanawal`
--
ALTER TABLE `tampilanawal`
  MODIFY `idTampilanAwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `idTransaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `varianbarang`
--
ALTER TABLE `varianbarang`
  MODIFY `idVarian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kodeKategori`) REFERENCES `kategoribarang` (`kodeKategori`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`kodeBarang`) REFERENCES `barang` (`kodeBarang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`kodeTransaksi`) REFERENCES `transaksi` (`kodeTransaksi`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `gambarutama`
--
ALTER TABLE `gambarutama`
  ADD CONSTRAINT `gambarutama_ibfk_1` FOREIGN KEY (`kodeBarang`) REFERENCES `barang` (`kodeBarang`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `gambarvarian`
--
ALTER TABLE `gambarvarian`
  ADD CONSTRAINT `gambarvarian_ibfk_2` FOREIGN KEY (`kodeBarang`) REFERENCES `barang` (`kodeBarang`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`kodePelanggan`) REFERENCES `pelanggan` (`kodePelanggan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `varianbarang`
--
ALTER TABLE `varianbarang`
  ADD CONSTRAINT `varianbarang_ibfk_1` FOREIGN KEY (`kodeBarang`) REFERENCES `barang` (`kodeBarang`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `varianbarang_ibfk_2` FOREIGN KEY (`idGambarVarian`) REFERENCES `gambarvarian` (`idGambarVarian`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
