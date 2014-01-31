-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 25, 2014 at 06:20 PM
-- Server version: 5.6.11
-- PHP Version: 5.3.25

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `v_fuelstart`
--

-- --------------------------------------------------------

--
-- Table structure for table `ws_accounts`
--

CREATE TABLE IF NOT EXISTS `ws_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(255) DEFAULT NULL COMMENT 'username',
  `account_email` varchar(255) DEFAULT NULL COMMENT 'email',
  `account_salt` varchar(255) DEFAULT NULL COMMENT 'store salt for use when hashing password',
  `account_password` tinytext COMMENT 'password',
  `account_display_name` varchar(255) DEFAULT NULL COMMENT 'name for display on web to prevent show username.',
  `account_firstname` varchar(255) DEFAULT NULL COMMENT 'first name',
  `account_middlename` varchar(255) DEFAULT NULL COMMENT 'middle name',
  `account_lastname` varchar(255) DEFAULT NULL COMMENT 'last name',
  `account_birthdate` date DEFAULT NULL COMMENT 'birthdate store in date format (YYYY-mm-dd)',
  `account_avatar` varchar(255) DEFAULT NULL COMMENT 'avatar file. refer from root web without http or domain',
  `account_signature` text COMMENT 'signature. very useful in forum',
  `account_timezone` varchar(30) NOT NULL DEFAULT 'Asia/Bangkok' COMMENT 'see timezone list here http://www.php.net/manual/en/timezones.php',
  `account_language` varchar(10) DEFAULT NULL COMMENT 'framework language shortcode eg: en, th',
  `account_create` bigint(20) DEFAULT NULL COMMENT 'timestamp of account create date',
  `account_create_gmt` bigint(20) DEFAULT NULL COMMENT 'timestamp of account create date in gmt0',
  `account_last_login` bigint(20) DEFAULT NULL COMMENT 'timestamp of last login date',
  `account_last_login_gmt` bigint(20) DEFAULT NULL COMMENT 'timestamp of last login date in gmt0',
  `account_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=disable, 1=enable',
  `account_status_text` varchar(255) DEFAULT NULL COMMENT 'status text for describe why disable.',
  `account_new_email` varchar(255) DEFAULT NULL COMMENT 'store new email waiting for confirmation',
  `account_new_password` varchar(255) DEFAULT NULL COMMENT 'store new password in reset password progress',
  `account_confirm_code` varchar(255) DEFAULT NULL COMMENT 'confirmation code. use for confirm register, change email, reset password',
  `account_confirm_code_since` bigint(20) DEFAULT NULL COMMENT 'confirm code generated since',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain user account' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ws_accounts`
--

INSERT INTO `ws_accounts` (`account_id`, `account_username`, `account_email`, `account_salt`, `account_password`, `account_display_name`, `account_firstname`, `account_middlename`, `account_lastname`, `account_birthdate`, `account_avatar`, `account_signature`, `account_timezone`, `account_language`, `account_create`, `account_create_gmt`, `account_last_login`, `account_last_login_gmt`, `account_status`, `account_status_text`, `account_new_email`, `account_new_password`, `account_confirm_code`, `account_confirm_code_since`) VALUES
(0, 'Guest', 'none@localhost', NULL, NULL, 'Guest', NULL, NULL, NULL, NULL, NULL, NULL, 'Asia/Bangkok', NULL, 1387121127, 1387095927, NULL, NULL, 0, 'This account is for guest actions.', NULL, NULL, NULL, NULL),
(1, 'admin', 'admin@localhost.com', NULL, '$2a$12$mPxupqGhPePgQAPvCpVUqekNfh.cAVusmgQyz1ZTfkcVLN0GBT7am', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, 'Asia/Bangkok', NULL, 1387121127, 1387095927, 1390635993, 1390610793, 1, NULL, NULL, NULL, NULL, NULL),
(2, '<div>', 'user@localhost.com', NULL, '$2a$12$5PcKjHhfevh8/v2fy1yVQ.b5FKe7pwhJbZExpo7qVT2SDwgZl1Jq2', '&lt;div&gt;', NULL, NULL, NULL, NULL, NULL, NULL, 'Asia/Bangkok', NULL, 1387946399, 1387921199, 1389092275, 1389067075, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ws_account_fields`
--

CREATE TABLE IF NOT EXISTS `ws_account_fields` (
  `account_id` int(11) NOT NULL COMMENT 'refer to accounts.account_id',
  `field_name` varchar(255) DEFAULT NULL,
  `field_value` text,
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ws_account_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `ws_account_level`
--

CREATE TABLE IF NOT EXISTS `ws_account_level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_group_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`level_id`),
  KEY `level_group_id` (`level_group_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ws_account_level`
--

INSERT INTO `ws_account_level` (`level_id`, `level_group_id`, `account_id`) VALUES
(1, 4, 0),
(2, 1, 1),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ws_account_level_group`
--

CREATE TABLE IF NOT EXISTS `ws_account_level_group` (
  `level_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) DEFAULT NULL,
  `level_description` text,
  `level_priority` int(5) NOT NULL DEFAULT '1' COMMENT 'lower is more higher priority',
  PRIMARY KEY (`level_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain user role or level' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ws_account_level_group`
--

INSERT INTO `ws_account_level_group` (`level_group_id`, `level_name`, `level_description`, `level_priority`) VALUES
(1, 'Super administrator', 'For site owner or super administrator.', 1),
(2, 'Administrator', NULL, 2),
(3, 'Member', 'For registered user.', 999),
(4, 'Guest', 'For non register user.', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `ws_account_level_permission`
--

CREATE TABLE IF NOT EXISTS `ws_account_level_permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_group_id` int(11) NOT NULL,
  `permission_core` int(1) NOT NULL DEFAULT '0' COMMENT '1=core permission, 0=modules permission',
  `module_system_name` varchar(255) DEFAULT NULL COMMENT 'module system name',
  `permission_page` varchar(255) NOT NULL,
  `permission_action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `level_group_id` (`level_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain permission for each admin page and action' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ws_account_level_permission`
--


-- --------------------------------------------------------

--
-- Table structure for table `ws_account_logins`
--

CREATE TABLE IF NOT EXISTS `ws_account_logins` (
  `account_login_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL COMMENT 'site id for multisite',
  `login_ua` varchar(255) DEFAULT NULL COMMENT 'user agent',
  `login_os` varchar(255) DEFAULT NULL COMMENT 'operating system',
  `login_browser` varchar(255) DEFAULT NULL COMMENT 'web browser',
  `login_ip` varchar(50) DEFAULT NULL COMMENT 'ip address',
  `login_time` bigint(20) DEFAULT NULL COMMENT 'login date time',
  `login_time_gmt` bigint(20) DEFAULT NULL COMMENT 'login date time in gmt 0',
  `login_attempt` int(1) NOT NULL DEFAULT '0' COMMENT '0=fail, 1=success',
  `login_attempt_text` varchar(255) DEFAULT NULL COMMENT 'login attempt text for describe what happen',
  PRIMARY KEY (`account_login_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain login history' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ws_account_logins`
--


-- --------------------------------------------------------

--
-- Table structure for table `ws_account_sites`
--

CREATE TABLE IF NOT EXISTS `ws_account_sites` (
  `account_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL COMMENT 'refer to accounts.account_id',
  `site_id` int(11) DEFAULT NULL COMMENT 'refer to sites.site_id for use with multi site',
  `account_last_login` bigint(20) DEFAULT NULL COMMENT 'last login date time',
  `account_last_login_gmt` bigint(20) DEFAULT NULL COMMENT 'last login date time in gmt 0',
  `account_online_code` varchar(255) DEFAULT NULL COMMENT 'store session code for check dubplicate log in if enabled.',
  PRIMARY KEY (`account_site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain account online code for each site (if use multisite)' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ws_account_sites`
--

INSERT INTO `ws_account_sites` (`account_site_id`, `account_id`, `site_id`, `account_last_login`, `account_last_login_gmt`, `account_online_code`) VALUES
(1, 0, 1, NULL, NULL, NULL),
(2, 1, 1, 1390635993, 1390610793, '2e94ab61d11b654913d64a4a21149667'),
(3, 2, 1, 1389092275, 1389067075, '152d06e5f69191a2da5664ca96fbdb02');

-- --------------------------------------------------------

--
-- Table structure for table `ws_config`
--

CREATE TABLE IF NOT EXISTS `ws_config` (
  `config_name` varchar(255) DEFAULT NULL COMMENT 'config name',
  `config_value` varchar(255) DEFAULT NULL COMMENT 'config value',
  `config_core` int(1) DEFAULT '0' COMMENT '0=no, 1=yes. if config core then please do not delete from db.',
  `config_description` text COMMENT 'description for this config',
  KEY `config_name` (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ws_config`
--

INSERT INTO `ws_config` (`config_name`, `config_value`, `config_core`, `config_description`) VALUES
('site_name', 'Fuel Start', 1, 'website name'),
('page_title_separator', ' | ', 1, 'page title separator. eg. site name | page'),
('site_timezone', 'Asia/Bangkok', 1, 'website default timezone'),
('simultaneous_login', '0', 1, 'allow log in more than 1 place, session? set to 1/0 to allow/disallow.'),
('allow_avatar', '1', 1, 'set to 1 if use avatar or set to 0 if not use it.'),
('avatar_size', '200', 1, 'set file size in Kilobyte.'),
('avatar_allowed_types', 'gif|jpg|png', 1, 'avatar allowe file types\r\neg. gif|jpg|png'),
('avatar_path', 'public/upload/avatar/', 1, 'path to directory for upload avatar. end with slash trail.'),
('member_allow_register', '1', 1, 'allow users to register'),
('member_register_notify_admin', '1', 1, 'send email to notify admin when new member register?'),
('member_verification', '1', 1, 'member verification method.\r\n0 = not verify\r\n1 = verify by email\r\n2 = wait for admin verify'),
('member_admin_verify_emails', 'admin@localhost', 1, 'emails of administrators to notice them when new member registration.\r\nfor multiple emails, use comma(, ) to seperate emails.'),
('member_disallow_username', 'admin, administrator, administrators, root, system', 1, 'Disallow username. Users cannot register their account with these username. seperate each username with comma (, ).\r\nThis data should not html encode.'),
('member_max_login_fail', '10', 1, 'Maximum continuous login failed limit. Set to 0 for unlimit (NOT recommend).'),
('member_login_fail_wait_time', '30', 1, 'Wait time for login failed. (value is minute)'),
('member_login_remember_length', '30', 1, 'How many days to remember login if user logged in with remember option? Unit is in days.'),
('member_confirm_wait_time', '10', 1, 'Confirmation action wait time. For use in reset password, change email. Unit is in munites.'),
('member_email_change_need_confirm', '1', 1, 'When email change, Does user need to confirm change by clicking on confirm link send to old email? \r\n1=Yes, 0=No'),
('mail_protocol', 'mail', 1, 'The mail sending protocol.\r\nmail, sendmail, smtp'),
('mail_mailpath', '/usr/sbin/sendmail', 1, 'The server path to Sendmail.'),
('mail_smtp_host', '', 1, 'SMTP Server Address.'),
('mail_smtp_user', '', 1, 'SMTP Username.'),
('mail_smtp_pass', '', 1, 'SMTP Password.'),
('mail_smtp_port', '25', 1, 'SMTP Port.'),
('mail_sender_email', 'no-reply@localhost', 1, 'Email for ''sender'''),
('content_items_perpage', '10', 1, 'number of items per page.'),
('content_admin_items_perpage', '20', 1, 'number of items per page in admin section'),
('media_allowed_types', '7z|aac|ace|ai|aif|aifc|aiff|avi|bmp|css|csv|doc|docx|eml|flv|gif|gz|h264|h.264|htm|html|jpeg|jpg|js|json|log|mid|midi|mov|mp3|mpeg|mpg|pdf|png|ppt|psd|swf|tar|text|tgz|tif|tiff|txt|wav|webm|word|xls|xlsx|xml|xsl|zip', 1, 'media upload allowed file types.'),
('ftp_host', '', 1, 'FTP host name. ftp is very useful in update/download files from remote host to current host.'),
('ftp_username', '', 1, 'FTP username'),
('ftp_password', '', 1, 'FTP password'),
('ftp_port', '21', 1, 'FTP port. usually is 21'),
('ftp_passive', 'true', 1, 'FTP passive mode'),
('ftp_basepath', '/public_html/', 1, 'FTP base path. store path to public html (web root)');

-- --------------------------------------------------------

--
-- Table structure for table `ws_sessions`
--

CREATE TABLE IF NOT EXISTS `ws_sessions` (
  `session_id` varchar(40) NOT NULL,
  `previous_id` varchar(40) NOT NULL,
  `user_agent` text NOT NULL,
  `ip_hash` char(32) NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) unsigned NOT NULL DEFAULT '0',
  `payload` longtext NOT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `PREVIOUS` (`previous_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ws_sessions`
--
