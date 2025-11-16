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

 Date: 17/11/2025 01:24:18
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
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of barang
-- ----------------------------
INSERT INTO `barang` VALUES (10, 'BG-001', 'Beras 10KG', 'Karung', 2, 1, 80000.00);
INSERT INTO `barang` VALUES (11, 'BG-002', 'Tepun', 'KG', 2, 1, 10000.00);

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
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of penerimaan_barang
-- ----------------------------
INSERT INTO `penerimaan_barang` VALUES (6, 'TR-20251116-001', 10, 10, '2025-11-16', 'supplier', 3, NULL, 1);
INSERT INTO `penerimaan_barang` VALUES (7, 'TR-20251116-001', 11, 10, '2025-11-16', 'supplier', 3, NULL, 1);
INSERT INTO `penerimaan_barang` VALUES (8, 'TR-20251116-002', 10, 1, '2025-11-16', 'admin', NULL, 1, 1);
INSERT INTO `penerimaan_barang` VALUES (9, 'TR-20251116-002', 11, 2, '2025-11-16', 'admin', NULL, 1, 1);

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
  `status` enum('diterima','dikirim') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_pengeluaran_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_pengeluaran_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_kode_pengeluaran`(`kode_pengeluaran` ASC) USING BTREE,
  INDEX `idx_tgl_keluar`(`tanggal_keluar` ASC) USING BTREE,
  CONSTRAINT `fk_pengeluaran_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pengeluaran_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengeluaran_barang
-- ----------------------------
INSERT INTO `pengeluaran_barang` VALUES (4, 'OUT-20251116-001', 10, 1, '2025-11-16', 'Outlet A', 1, 'diterima');
INSERT INTO `pengeluaran_barang` VALUES (5, 'OUT-20251116-001', 11, 2, '2025-11-16', 'Outlet A', 1, 'diterima');

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
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_po_barang`(`barang_id` ASC) USING BTREE,
  INDEX `fk_po_supplier`(`supplier_id` ASC) USING BTREE,
  INDEX `idx_kode_po`(`kode_po` ASC) USING BTREE,
  INDEX `idx_tgl_po`(`tanggal_pemesanan` ASC) USING BTREE,
  CONSTRAINT `fk_po_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pre_order_barang
-- ----------------------------
INSERT INTO `pre_order_barang` VALUES (8, 'PO-20251116-001', 10, 10, '2025-11-16', 3, 'diterima');
INSERT INTO `pre_order_barang` VALUES (9, 'PO-20251116-001', 11, 10, '2025-11-16', 3, 'diterima');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', '2025-11-13 14:16:19', '2025-11-13 14:16:19', 'Admin');
INSERT INTO `users` VALUES (4, 'Sukamto', 'owner', '5be057accb25758101fa5eadbbd79503', '2025-11-13 14:55:24', '2025-11-17 01:22:30', 'Owner');
INSERT INTO `users` VALUES (5, 'Jono', 'karyawan', '07142c5501c3ea09303d899012e2b47d', '2025-11-17 01:20:55', '2025-11-17 01:20:55', 'Karyawan');

-- ----------------------------
-- View structure for v_current_stock
-- ----------------------------
DROP VIEW IF EXISTS `v_current_stock`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_current_stock` AS select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`stok_minimal` AS `stok_minimal`,`kb`.`nama_kategori` AS `nama_kategori`,coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) AS `total_penerimaan`,coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0) AS `total_pengeluaran`,coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0) AS `total_retur`,((coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) - coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0)) - coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0)) AS `stok_tersedia`,(case when (((coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) - coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0)) - coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0)) <= 0) then 'HABIS' when (((coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) - coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0)) - coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0)) <= `b`.`stok_minimal`) then 'KRITIS' when (((coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) - coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0)) - coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0)) <= (`b`.`stok_minimal` * 1.5)) then 'RENDAH' else 'AMAN' end) AS `status_stok`,`b`.`harga_beli` AS `harga_beli`,(((coalesce((select sum(`penerimaan_barang`.`jumlah`) from `penerimaan_barang` where (`penerimaan_barang`.`barang_id` = `b`.`id`)),0) - coalesce((select sum(`pengeluaran_barang`.`jumlah`) from `pengeluaran_barang` where (`pengeluaran_barang`.`barang_id` = `b`.`id`)),0)) - coalesce((select sum(`retur_barang`.`jumlah`) from `retur_barang` where (`retur_barang`.`barang_id` = `b`.`id`)),0)) * `b`.`harga_beli`) AS `nilai_stok` from (`barang` `b` left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`)));

-- ----------------------------
-- View structure for v_daily_stock_summary
-- ----------------------------
DROP VIEW IF EXISTS `v_daily_stock_summary`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_daily_stock_summary` AS select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`kb`.`nama_kategori` AS `nama_kategori`,coalesce(cast(`pe`.`tanggal_penerimaan` as date),cast(`pg`.`tanggal_keluar` as date),cast(`r`.`tanggal_retur` as date)) AS `tanggal`,coalesce(sum(`pe`.`jumlah`),0) AS `stok_masuk`,(coalesce(sum(`pg`.`jumlah`),0) + coalesce(sum(`r`.`jumlah`),0)) AS `stok_keluar`,coalesce(sum(`pg`.`jumlah`),0) AS `pengeluaran`,coalesce(sum(`r`.`jumlah`),0) AS `retur`,(coalesce(sum(`pe`.`jumlah`),0) - (coalesce(sum(`pg`.`jumlah`),0) + coalesce(sum(`r`.`jumlah`),0))) AS `selisih` from ((((`barang` `b` left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`))) left join `penerimaan_barang` `pe` on((`b`.`id` = `pe`.`barang_id`))) left join `pengeluaran_barang` `pg` on(((`b`.`id` = `pg`.`barang_id`) and (cast(`pg`.`tanggal_keluar` as date) = cast(`pe`.`tanggal_penerimaan` as date))))) left join `retur_barang` `r` on(((`b`.`id` = `r`.`barang_id`) and (cast(`r`.`tanggal_retur` as date) = coalesce(cast(`pe`.`tanggal_penerimaan` as date),cast(`pg`.`tanggal_keluar` as date)))))) where ((`pe`.`tanggal_penerimaan` is not null) or (`pg`.`tanggal_keluar` is not null) or (`r`.`tanggal_retur` is not null)) group by `b`.`id`,`b`.`kode_barang`,`b`.`nama_barang`,`b`.`satuan`,`kb`.`nama_kategori`,`tanggal`;

-- ----------------------------
-- View structure for v_monthly_stock_summary
-- ----------------------------
DROP VIEW IF EXISTS `v_monthly_stock_summary`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_monthly_stock_summary` AS select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`kb`.`nama_kategori` AS `nama_kategori`,year(coalesce(`pe`.`tanggal_penerimaan`,`pg`.`tanggal_keluar`,`r`.`tanggal_retur`)) AS `tahun`,month(coalesce(`pe`.`tanggal_penerimaan`,`pg`.`tanggal_keluar`,`r`.`tanggal_retur`)) AS `bulan`,coalesce(sum(`pe`.`jumlah`),0) AS `stok_masuk`,coalesce(sum(`pg`.`jumlah`),0) AS `stok_keluar`,coalesce(sum(`r`.`jumlah`),0) AS `stok_retur`,((coalesce(sum(`pe`.`jumlah`),0) - coalesce(sum(`pg`.`jumlah`),0)) - coalesce(sum(`r`.`jumlah`),0)) AS `selisih` from ((((`barang` `b` left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`))) left join `penerimaan_barang` `pe` on((`b`.`id` = `pe`.`barang_id`))) left join `pengeluaran_barang` `pg` on(((`b`.`id` = `pg`.`barang_id`) and (year(`pg`.`tanggal_keluar`) = year(`pe`.`tanggal_penerimaan`)) and (month(`pg`.`tanggal_keluar`) = month(`pe`.`tanggal_penerimaan`))))) left join `retur_barang` `r` on(((`b`.`id` = `r`.`barang_id`) and (year(`r`.`tanggal_retur`) = year(coalesce(`pe`.`tanggal_penerimaan`,`pg`.`tanggal_keluar`))) and (month(`r`.`tanggal_retur`) = month(coalesce(`pe`.`tanggal_penerimaan`,`pg`.`tanggal_keluar`)))))) where ((`pe`.`tanggal_penerimaan` is not null) or (`pg`.`tanggal_keluar` is not null) or (`r`.`tanggal_retur` is not null)) group by `b`.`id`,`b`.`kode_barang`,`b`.`nama_barang`,`b`.`satuan`,`kb`.`nama_kategori`,`tahun`,`bulan`;

-- ----------------------------
-- View structure for v_pre_order_details
-- ----------------------------
DROP VIEW IF EXISTS `v_pre_order_details`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_pre_order_details` AS select `po`.`id` AS `id`,`po`.`kode_po` AS `kode_po`,`po`.`barang_id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`harga_beli` AS `harga_beli`,`po`.`jumlah` AS `jumlah`,(`po`.`jumlah` * `b`.`harga_beli`) AS `subtotal`,`po`.`tanggal_pemesanan` AS `tanggal_pemesanan`,`po`.`status` AS `status`,`po`.`supplier_id` AS `supplier_id`,`s`.`nama_supplier` AS `nama_supplier`,`s`.`kontak` AS `kontak_supplier`,`s`.`alamat` AS `alamat_supplier` from ((`pre_order_barang` `po` join `barang` `b` on((`po`.`barang_id` = `b`.`id`))) join `suppliers` `s` on((`po`.`supplier_id` = `s`.`id`)));

-- ----------------------------
-- View structure for v_retur_details
-- ----------------------------
DROP VIEW IF EXISTS `v_retur_details`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_retur_details` AS select `r`.`id` AS `id`,`r`.`kode_retur` AS `kode_retur`,`r`.`tanggal_retur` AS `tanggal_retur`,`r`.`barang_id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`harga_beli` AS `harga_beli`,`r`.`jumlah` AS `jumlah`,(`r`.`jumlah` * `b`.`harga_beli`) AS `nilai_retur`,`r`.`penyebab_retur` AS `penyebab_retur`,`r`.`supplier_id` AS `supplier_id`,`s`.`nama_supplier` AS `nama_supplier`,`s`.`kontak` AS `kontak_supplier`,`r`.`user_id` AS `user_id`,`u`.`nama` AS `nama_user`,`kb`.`nama_kategori` AS `nama_kategori` from ((((`retur_barang` `r` join `barang` `b` on((`r`.`barang_id` = `b`.`id`))) join `suppliers` `s` on((`r`.`supplier_id` = `s`.`id`))) left join `users` `u` on((`r`.`user_id` = `u`.`id`))) left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`)));

-- ----------------------------
-- View structure for v_retur_summary
-- ----------------------------
DROP VIEW IF EXISTS `v_retur_summary`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_retur_summary` AS select `s`.`id` AS `supplier_id`,`s`.`nama_supplier` AS `nama_supplier`,`s`.`kontak` AS `kontak`,count(distinct `r`.`kode_retur`) AS `jumlah_transaksi_retur`,count(`r`.`id`) AS `jumlah_item_retur`,sum(`r`.`jumlah`) AS `total_qty_retur`,min(`r`.`tanggal_retur`) AS `retur_pertama`,max(`r`.`tanggal_retur`) AS `retur_terakhir` from (`suppliers` `s` left join `retur_barang` `r` on((`s`.`id` = `r`.`supplier_id`))) group by `s`.`id`,`s`.`nama_supplier`,`s`.`kontak` having (`jumlah_transaksi_retur` > 0) order by `total_qty_retur` desc;

-- ----------------------------
-- View structure for v_stock_alert
-- ----------------------------
DROP VIEW IF EXISTS `v_stock_alert`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_stock_alert` AS select `v_current_stock`.`barang_id` AS `barang_id`,`v_current_stock`.`kode_barang` AS `kode_barang`,`v_current_stock`.`nama_barang` AS `nama_barang`,`v_current_stock`.`satuan` AS `satuan`,`v_current_stock`.`stok_minimal` AS `stok_minimal`,`v_current_stock`.`stok_tersedia` AS `stok_tersedia`,`v_current_stock`.`status_stok` AS `status_stok`,(case when (`v_current_stock`.`stok_tersedia` <= 0) then `v_current_stock`.`stok_minimal` else (`v_current_stock`.`stok_minimal` - `v_current_stock`.`stok_tersedia`) end) AS `kekurangan`,`v_current_stock`.`nama_kategori` AS `nama_kategori`,`v_current_stock`.`total_pengeluaran` AS `total_pengeluaran`,`v_current_stock`.`total_retur` AS `total_retur`,(`v_current_stock`.`total_pengeluaran` + `v_current_stock`.`total_retur`) AS `total_stock_out` from `v_current_stock` where (`v_current_stock`.`status_stok` in ('HABIS','KRITIS','RENDAH')) order by (case `v_current_stock`.`status_stok` when 'HABIS' then 1 when 'KRITIS' then 2 when 'RENDAH' then 3 else 4 end),`v_current_stock`.`stok_tersedia`;

-- ----------------------------
-- View structure for v_stock_movements
-- ----------------------------
DROP VIEW IF EXISTS `v_stock_movements`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_stock_movements` AS select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`kategori_id` AS `kategori_id`,`kb`.`nama_kategori` AS `nama_kategori`,'IN' AS `tipe_transaksi`,'PENERIMAAN' AS `jenis_transaksi`,`pe`.`tanggal_penerimaan` AS `tanggal_transaksi`,`pe`.`kode_penerimaan` AS `kode_transaksi`,`pe`.`jumlah` AS `jumlah`,`pe`.`tipe_penerimaan` AS `tipe_penerimaan`,(case when (`pe`.`tipe_penerimaan` = 'supplier') then `s`.`nama_supplier` when (`pe`.`tipe_penerimaan` = 'admin') then `u_dari`.`nama` else 'Unknown' end) AS `sumber`,`u_penerima`.`nama` AS `penanggung_jawab`,`pe`.`supplier_id` AS `supplier_id`,`pe`.`dari_user_id` AS `dari_user_id`,`pe`.`penerima_user_id` AS `user_id`,NULL AS `penyebab` from (((((`penerimaan_barang` `pe` join `barang` `b` on((`pe`.`barang_id` = `b`.`id`))) left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`))) left join `suppliers` `s` on((`pe`.`supplier_id` = `s`.`id`))) left join `users` `u_dari` on((`pe`.`dari_user_id` = `u_dari`.`id`))) left join `users` `u_penerima` on((`pe`.`penerima_user_id` = `u_penerima`.`id`))) union all select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`kategori_id` AS `kategori_id`,`kb`.`nama_kategori` AS `nama_kategori`,'OUT' AS `tipe_transaksi`,'PENGELUARAN' AS `jenis_transaksi`,`pg`.`tanggal_keluar` AS `tanggal_transaksi`,`pg`.`kode_pengeluaran` AS `kode_transaksi`,`pg`.`jumlah` AS `jumlah`,NULL AS `tipe_penerimaan`,`pg`.`tujuan_barang` AS `sumber`,`u`.`nama` AS `penanggung_jawab`,NULL AS `supplier_id`,NULL AS `dari_user_id`,`pg`.`user_id` AS `user_id`,NULL AS `penyebab` from (((`pengeluaran_barang` `pg` join `barang` `b` on((`pg`.`barang_id` = `b`.`id`))) left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`))) left join `users` `u` on((`pg`.`user_id` = `u`.`id`))) union all select `b`.`id` AS `barang_id`,`b`.`kode_barang` AS `kode_barang`,`b`.`nama_barang` AS `nama_barang`,`b`.`satuan` AS `satuan`,`b`.`kategori_id` AS `kategori_id`,`kb`.`nama_kategori` AS `nama_kategori`,'OUT' AS `tipe_transaksi`,'RETUR' AS `jenis_transaksi`,`r`.`tanggal_retur` AS `tanggal_transaksi`,`r`.`kode_retur` AS `kode_transaksi`,`r`.`jumlah` AS `jumlah`,NULL AS `tipe_penerimaan`,concat('Retur ke ',`s`.`nama_supplier`) AS `sumber`,`u`.`nama` AS `penanggung_jawab`,`r`.`supplier_id` AS `supplier_id`,NULL AS `dari_user_id`,`r`.`user_id` AS `user_id`,`r`.`penyebab_retur` AS `penyebab` from ((((`retur_barang` `r` join `barang` `b` on((`r`.`barang_id` = `b`.`id`))) left join `kategori_barang` `kb` on((`b`.`kategori_id` = `kb`.`id`))) join `suppliers` `s` on((`r`.`supplier_id` = `s`.`id`))) left join `users` `u` on((`r`.`user_id` = `u`.`id`)));

SET FOREIGN_KEY_CHECKS = 1;
