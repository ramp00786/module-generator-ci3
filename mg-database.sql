-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 19, 2023 at 07:16 AM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mg`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
CREATE TABLE IF NOT EXISTS `acl` (
  `ai` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`ai`),
  KEY `action_id` (`action_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `acl_actions`
--

DROP TABLE IF EXISTS `acl_actions`;
CREATE TABLE IF NOT EXISTS `acl_actions` (
  `action_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_code` varchar(100) NOT NULL COMMENT 'No periods allowed!',
  `action_desc` varchar(100) NOT NULL COMMENT 'Human readable description',
  `category_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`action_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `acl_categories`
--

DROP TABLE IF EXISTS `acl_categories`;
CREATE TABLE IF NOT EXISTS `acl_categories` (
  `category_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_code` varchar(100) NOT NULL COMMENT 'No periods allowed!',
  `category_desc` varchar(100) NOT NULL COMMENT 'Human readable description',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_code` (`category_code`),
  UNIQUE KEY `category_desc` (`category_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `auth_sessions`
--

DROP TABLE IF EXISTS `auth_sessions`;
CREATE TABLE IF NOT EXISTS `auth_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `auth_sessions`
--

INSERT INTO `auth_sessions` (`id`, `user_id`, `login_time`, `modified_at`, `ip_address`, `user_agent`) VALUES
('ouum4gdfm32kcr3tdm92i25m1udestn5', 3996974045, '2023-07-19 05:50:53', '2023-07-19 07:15:53', '::1', 'Chrome 114.0.0.0 on Windows 10'),
('59cep7pj72p2226o9c5vdi56uvdetj4m', 507038834, '2023-07-19 06:24:31', '2023-07-19 06:24:31', '::1', 'Chrome 114.0.0.0 on Windows 10'),
('0ggpg23d5pettfnfpjak38gtstf0lq12', 3996974044, '2023-07-18 06:13:50', '2023-07-18 06:13:50', '::1', 'Chrome 114.0.0.0 on Windows 10'),
('chorc1lpa9p2b7s9063grre3purkcvrv', 3996974044, '2023-07-18 10:28:55', '2023-07-18 12:21:58', '::1', 'Chrome 114.0.0.0 on Windows 10');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `denied_access`
--

DROP TABLE IF EXISTS `denied_access`;
CREATE TABLE IF NOT EXISTS `denied_access` (
  `ai` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `time` datetime NOT NULL,
  `reason_code` tinyint UNSIGNED DEFAULT '0',
  PRIMARY KEY (`ai`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dropdown_data`
--

DROP TABLE IF EXISTS `dropdown_data`;
CREATE TABLE IF NOT EXISTS `dropdown_data` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` varchar(255) DEFAULT NULL,
  `field_id` varchar(255) DEFAULT NULL,
  `op_name` varchar(255) DEFAULT NULL,
  `op_val` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `field_select_box_table` varchar(255) DEFAULT NULL,
  `field_select_box_table_column` varchar(255) DEFAULT NULL,
  `display_in_form` varchar(255) DEFAULT NULL,
  `db_module_have_child` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edit_details`
--

DROP TABLE IF EXISTS `edit_details`;
CREATE TABLE IF NOT EXISTS `edit_details` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) NOT NULL,
  `edit_date` varchar(255) NOT NULL,
  `edit_by` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `row_id` varchar(255) NOT NULL,
  `old_record` text,
  `new_record` text,
  `ip_address` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `view_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `field_attributes`
--

DROP TABLE IF EXISTS `field_attributes`;
CREATE TABLE IF NOT EXISTS `field_attributes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` varchar(255) DEFAULT NULL,
  `field_id` varchar(255) DEFAULT NULL,
  `attribute_name` varchar(255) DEFAULT NULL,
  `attribute_value` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ips_on_hold`
--

DROP TABLE IF EXISTS `ips_on_hold`;
CREATE TABLE IF NOT EXISTS `ips_on_hold` (
  `ai` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`ai`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `login_errors`
--

DROP TABLE IF EXISTS `login_errors`;
CREATE TABLE IF NOT EXISTS `login_errors` (
  `ai` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username_or_email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`ai`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `login_errors`
--

INSERT INTO `login_errors` (`ai`, `username_or_email`, `ip_address`, `time`) VALUES
(51, 'john@gmail.com', '::1', '2023-07-19 06:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` int DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `module_fields`
--

DROP TABLE IF EXISTS `module_fields`;
CREATE TABLE IF NOT EXISTS `module_fields` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_name` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `field_label` varchar(255) DEFAULT NULL,
  `field_duplicate` varchar(255) DEFAULT NULL,
  `module_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `field_select_box_type` varchar(255) DEFAULT NULL,
  `field_display_status` varchar(50) DEFAULT NULL,
  `input_type` varchar(255) DEFAULT NULL,
  `module_parent` varchar(255) DEFAULT NULL,
  `child_module_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
CREATE TABLE IF NOT EXISTS `password_history` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `update_date` varchar(255) NOT NULL,
  `update_time` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `tables_relation`
--

DROP TABLE IF EXISTS `tables_relation`;
CREATE TABLE IF NOT EXISTS `tables_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_table_id` varchar(255) DEFAULT NULL,
  `child_table_id` varchar(255) DEFAULT NULL,
  `parent_col_id` varchar(255) DEFAULT NULL,
  `child_col_id` varchar(255) DEFAULT NULL,
  `status` smallint DEFAULT '0',
  `ip_address` varchar(50) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL,
  `browser` varchar(20) DEFAULT NULL,
  `date` varchar(50) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `username_or_email_on_hold`
--

DROP TABLE IF EXISTS `username_or_email_on_hold`;
CREATE TABLE IF NOT EXISTS `username_or_email_on_hold` (
  `ai` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username_or_email` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`ai`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int UNSIGNED NOT NULL,
  `username` varchar(12) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `auth_level` tinyint UNSIGNED NOT NULL,
  `banned` enum('0','1') NOT NULL DEFAULT '0',
  `passwd` varchar(60) NOT NULL,
  `passwd_recovery_code` varchar(60) DEFAULT NULL,
  `passwd_recovery_date` datetime DEFAULT NULL,
  `passwd_modified_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `auth_level`, `banned`, `passwd`, `passwd_recovery_code`, `passwd_recovery_date`, `passwd_modified_at`, `last_login`, `created_at`, `modified_at`, `first_name`, `last_name`, `contact_number`, `address`, `api_key`, `token`, `status`, `ip_address`, `os`, `browser`, `date`, `time`) VALUES
(3320029121, 'john.doe', 'john.doe@gmail.com', 1, '0', '$2y$10$2NIRuYZDgTLa.XlsuxBhu.QicY2Lyq8w0y5vXQ.CY0qMu.nxuzzKm', NULL, NULL, NULL, NULL, '2023-07-19 06:33:22', '2023-07-19 06:33:22', 'John', 'Doe', '9999999999', 'CC', '', 'a0388f33', '1', '::1', 'Windows 10', 'Chrome', '2023-07-19', '12:03 PM'),
(3996974045, 'SuperAdmin', 'admin@admin.com', 9, '0', '$2y$11$7uzaj78Vn1khVJ03f3p6p.7BgvcVM6EniVfKEG3JLGHLHzyTiOche', NULL, NULL, '2019-08-22 12:46:52', '2023-07-19 05:50:53', '2019-08-20 11:22:52', '2023-07-19 05:50:53', 'SuperAdmin', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `module_id` varchar(255) DEFAULT NULL,
  `show_items` varchar(255) DEFAULT NULL,
  `create_items` varchar(255) DEFAULT NULL,
  `update_items` varchar(255) DEFAULT NULL,
  `delete_items` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acl`
--
ALTER TABLE `acl`
  ADD CONSTRAINT `acl_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `acl_actions` (`action_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_actions`
--
ALTER TABLE `acl_actions`
  ADD CONSTRAINT `acl_actions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `acl_categories` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
