/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : 127.0.0.1:3306
 Source Schema         : takoyaki

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 14/11/2025 17:30:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for barang
-- ----------------------------
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_barang` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kategori_id` bigint UNSIGNED NULL DEFAULT NULL,
  `stok_minimal` int UNSIGNED NULL DEFAULT 0,
  `harga_beli` decimal(15, 2) NULL DEFAULT 0.00,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_barang`(`kode_barang` ASC) USING BTREE,
  INDEX `fk_barang_kategori`(`kategori_id` ASC) USING BTREE,
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of barang
-- ----------------------------
INSERT INTO `barang` VALUES (1, 'Adwd222', 'A', 'kg', 2, 11, 2800.00);
INSERT INTO `barang` VALUES (9, 'Asc221', 'CV', 'kg', 2, 11, 2800.00);

-- ----------------------------
-- Table structure for kategori_barang
-- ----------------------------
DROP TABLE IF EXISTS `kategori_barang`;
CREATE TABLE `kategori_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kategori_barang
-- ----------------------------
INSERT INTO `kategori_barang` VALUES (2, 'Makanan', 'makanan berat');
INSERT INTO `kategori_barang` VALUES (5, 'Minuman', 'minuman ringan');

-- ----------------------------
-- Table structure for penerimaan_barang
-- ----------------------------
DROP TABLE IF EXISTS `penerimaan_barang`;
CREATE TABLE `penerimaan_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_penerimaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `jumlah` int UNSIGNED NOT NULL,
  `tanggal_penerimaan` date NOT NULL,
  `tipe_penerimaan` enum('supplier','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NULL DEFAULT NULL,
  `dari_user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `penerima_user_id` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_penerimaan_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_penerimaan_supplier`(`supplier_id` ASC) USING BTREE,
  INDEX `fk_penerimaan_dari_user`(`dari_user_id` ASC) USING BTREE,
  INDEX `fk_penerimaan_penerima_user`(`penerima_user_id` ASC) USING BTREE,
  INDEX `idx_kode_penerimaan`(`kode_penerimaan` ASC) USING BTREE,
  INDEX `idx_tgl_penerimaan`(`tanggal_penerimaan` ASC) USING BTREE,
  CONSTRAINT `fk_penerimaan_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_penerimaan_dari_user` FOREIGN KEY (`dari_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_penerimaan_penerima_user` FOREIGN KEY (`penerima_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_penerimaan_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of penerimaan_barang
-- ----------------------------

-- ----------------------------
-- Table structure for pengeluaran_barang
-- ----------------------------
DROP TABLE IF EXISTS `pengeluaran_barang`;
CREATE TABLE `pengeluaran_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_pengeluaran` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `jumlah` int UNSIGNED NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `tujuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_pengeluaran_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_pengeluaran_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_kode_pengeluaran`(`kode_pengeluaran` ASC) USING BTREE,
  INDEX `idx_tgl_keluar`(`tanggal_keluar` ASC) USING BTREE,
  CONSTRAINT `fk_pengeluaran_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pengeluaran_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengeluaran_barang
-- ----------------------------

-- ----------------------------
-- Table structure for pre_order_barang
-- ----------------------------
DROP TABLE IF EXISTS `pre_order_barang`;
CREATE TABLE `pre_order_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_po` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `jumlah` int UNSIGNED NOT NULL,
  `tanggal_pemesanan` date NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `status` enum('draft','dipesan','diterima','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'draft',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_po_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_po_supplier`(`supplier_id` ASC) USING BTREE,
  INDEX `fk_po_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_kode_po`(`kode_po` ASC) USING BTREE,
  INDEX `idx_tgl_po`(`tanggal_pemesanan` ASC) USING BTREE,
  CONSTRAINT `fk_po_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_po_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pre_order_barang
-- ----------------------------

-- ----------------------------
-- Table structure for retur_barang
-- ----------------------------
DROP TABLE IF EXISTS `retur_barang`;
CREATE TABLE `retur_barang`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_retur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `barang_id` bigint UNSIGNED NOT NULL,
  `jumlah` int UNSIGNED NOT NULL,
  `penyebab_retur` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_retur` date NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_retur_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_retur_supplier`(`supplier_id` ASC) USING BTREE,
  INDEX `fk_retur_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_kode_retur`(`kode_retur` ASC) USING BTREE,
  INDEX `idx_tgl_retur`(`tanggal_retur` ASC) USING BTREE,
  CONSTRAINT `fk_retur_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_retur_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_retur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of retur_barang
-- ----------------------------

-- ----------------------------
-- Table structure for suppliers
-- ----------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kontak` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of suppliers
-- ----------------------------
INSERT INTO `suppliers` VALUES (3, 'PT. Siba Surya', '22132', 'aa');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level` enum('Admin','Karyawan','Owner') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', '2025-11-13 14:16:19', '2025-11-13 14:16:19', 'Admin');
INSERT INTO `users` VALUES (4, 'Sukamto', 'owner', '202cb962ac59075b964b07152d234b70', '2025-11-13 14:55:24', '2025-11-13 14:55:30', 'Owner');

SET FOREIGN_KEY_CHECKS = 1;
