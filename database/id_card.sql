-- Student / Employee ID Card Generator in PHP
-- Database: `id_card` | Core PHP + MySQL
-- Updated clean schema (2026): same database, table and column names as the
-- original version, so existing installs keep working.
-- Changes vs the old dump:
--   * InnoDB + utf8mb4 (the old MyISAM + latin1 could not store many Indian /
--     international names correctly and had no transaction safety)
--   * Primary key + AUTO_INCREMENT declared in CREATE TABLE (the old dump
--     relied on later ALTER statements)
--   * `dob` and `exp_date` are real DATE columns (they were varchar)
--   * `email` widened 40 -> 150 chars; UNIQUE key on `id_no`
--     (an ID number must never repeat - the app also checks before insert)
--   * NO personal data rows are included - the old dump shipped a real
--     person's data. One clearly-fake demo row is provided (commented out).

CREATE DATABASE IF NOT EXISTS `id_card` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `id_card`;

CREATE TABLE IF NOT EXISTS `cards` (
  `sno` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `grade` varchar(20) DEFAULT NULL,
  `id_no` varchar(15) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `exp_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sno`),
  UNIQUE KEY `id_no_unique` (`id_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional demo row (fake data - safe to delete):
-- INSERT INTO `cards` (`name`, `grade`, `id_no`, `email`, `phone`, `address`, `dob`, `exp_date`, `image`)
-- VALUES ('Demo Student', '10th', '2026001', 'demo@example.com', '9876543210', 'New Delhi, India', '2010-05-14', '2027-03-31', 'assets/uploads/images.jpg');
