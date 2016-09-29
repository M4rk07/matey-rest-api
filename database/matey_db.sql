-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2016 at 02:02 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `matey_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_access_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth2_access_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) DEFAULT NULL,
  `token_type` varchar(255) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `scope` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_authorize`
--

CREATE TABLE IF NOT EXISTS `oauth2_authorize` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `scope` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oauth2_authorize`
--

INSERT INTO `oauth2_authorize` (`id`, `client_id`, `username`, `scope`) VALUES
(1, 'mmm', 'root', 'friends');

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_clients`
--

CREATE TABLE IF NOT EXISTS `oauth2_clients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_secret` varchar(255) DEFAULT NULL,
  `redirect_uri` text,
  `client_type` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oauth2_clients`
--

INSERT INTO `oauth2_clients` (`id`, `client_secret`, `redirect_uri`, `client_type`) VALUES
(1, 'mmm', 'https://www.facebook.com/', '');

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_codes`
--

CREATE TABLE IF NOT EXISTS `oauth2_codes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `redirect_uri` text,
  `expires` datetime DEFAULT NULL,
  `scope` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `oauth2_codes`
--

INSERT INTO `oauth2_codes` (`id`, `code`, `client_id`, `username`, `redirect_uri`, `expires`, `scope`) VALUES
(24, 'c04ed56e11cb88c4d390b8c4cbdb16fc', '1', 'marko@marko.com', 'https://www.facebook.com/', '2016-08-17 20:56:25', ''),
(25, '490aa67dfbffaabf17b93afd2049a7e4', '1', 'marko@marko.com', 'https://www.facebook.com/', '2016-08-17 20:59:00', ''),
(26, '411ed8e07c846f5266b3ee01b6e2ba0b', '1', 'marko@marko.com', 'https://www.facebook.com/', '2016-08-17 21:10:06', '');

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_refresh_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth2_refresh_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(255) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `scope` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth2_scopes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `oauth2_scopes`
--

INSERT INTO `oauth2_scopes` (`id`, `scope`) VALUES
(1, 'friends'),
(2, 'contacts');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fb_id` bigint(20) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `salt` varchar(32) NOT NULL,
  `roles` text,
  `phone_number` varchar(20) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `profile_picture_link` varchar(200) DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` int(11) NOT NULL DEFAULT '0',
  `gender` varchar(7) DEFAULT NULL,
  `birthday` varchar(11) DEFAULT NULL,
  `hometown` varchar(30) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `quote_status` varchar(120) DEFAULT NULL,
  `private` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fb_id`, `email`, `password`, `salt`, `roles`, `phone_number`, `first_name`, `last_name`, `profile_picture_link`, `date_registered`, `is_verified`, `gender`, `birthday`, `hometown`, `location`, `quote_status`, `private`) VALUES
(82, NULL, 'marko@marko.com', 'marko', '', NULL, NULL, 'Marko', 'Ognjenovic', NULL, '2016-08-16 16:57:58', 0, NULL, NULL, NULL, NULL, NULL, 0),
(83, NULL, 'radovan@gmail.com', NULL, '', NULL, NULL, 'Radovan', 'Ristovic', NULL, '2016-09-03 15:17:17', 0, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE IF NOT EXISTS `user_devices` (
  `device_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_got_live` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `board` varchar(30) DEFAULT NULL,
  `brand` varchar(30) DEFAULT NULL,
  `device` varchar(30) DEFAULT NULL,
  `model` varchar(30) DEFAULT NULL,
  `gcm_token` varchar(500) NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_fb_access_tokens`
--

CREATE TABLE IF NOT EXISTS `user_fb_access_tokens` (
  `fb_access_token` varchar(100) NOT NULL,
  `client_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `expires` datetime NOT NULL,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`fb_access_token`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_friends`
--

CREATE TABLE IF NOT EXISTS `user_friends` (
  `id_user_one` bigint(20) NOT NULL,
  `id_user_two` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `date_friends` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `user_one_id_2` (`id_user_one`),
  KEY `user_two_id` (`id_user_two`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_friends`
--

INSERT INTO `user_friends` (`id_user_one`, `id_user_two`, `status`, `date_friends`) VALUES
(82, 83, 1, '2016-09-03 17:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id_group` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator_id` bigint(20) NOT NULL,
  `group_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group`),
  KEY `creator_id` (`creator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Triggers `user_groups`
--
DROP TRIGGER IF EXISTS `automatic_join`;
DELIMITER //
CREATE TRIGGER `automatic_join` AFTER INSERT ON `user_groups`
 FOR EACH ROW INSERT INTO belongs (group_id, user_id) VALUES (NEW.group_id, NEW.creator_id)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group_belongs`
--

CREATE TABLE IF NOT EXISTS `user_group_belongs` (
  `group_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_interests`
--

CREATE TABLE IF NOT EXISTS `user_interests` (
  `id_interest` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id_interest`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_interests`
--

INSERT INTO `user_interests` (`id_interest`, `name`) VALUES
(1, 'Computers'),
(2, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE IF NOT EXISTS `user_logins` (
  `device_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `last_log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_logged` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `device_id` (`device_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_phone_verifications`
--

CREATE TABLE IF NOT EXISTS `user_phone_verifications` (
  `user_id` bigint(20) NOT NULL,
  `code` varchar(5) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_posts`
--

CREATE TABLE IF NOT EXISTS `user_posts` (
  `id_post` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) NOT NULL,
  `id_group` bigint(20) DEFAULT NULL,
  `text` varchar(320) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_post`),
  KEY `author_id` (`id_user`),
  KEY `group_id` (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_posts`
--

INSERT INTO `user_posts` (`id_post`, `id_user`, `id_group`, `text`, `date_created`) VALUES
(1, 82, NULL, 'Najvece smo baje, sta da se radi...', '2016-09-03 17:01:01'),
(2, 83, NULL, 'Ovo je neka druga objava..', '2016-09-03 18:47:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_post_files`
--

CREATE TABLE IF NOT EXISTS `user_post_files` (
  `file_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `filetype` varchar(10) NOT NULL,
  `filesize` double NOT NULL,
  `extension` varchar(7) NOT NULL,
  `url` varchar(200) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `deprecated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_post_interests`
--

CREATE TABLE IF NOT EXISTS `user_post_interests` (
  `id_post` bigint(20) NOT NULL,
  `id_interest` bigint(20) NOT NULL,
  KEY `id_post` (`id_post`),
  KEY `id_interest` (`id_interest`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_post_interests`
--

INSERT INTO `user_post_interests` (`id_post`, `id_interest`) VALUES
(1, 1),
(1, 2),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_post_replies`
--

CREATE TABLE IF NOT EXISTS `user_post_replies` (
  `id_reply` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_post` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` varchar(320) NOT NULL,
  PRIMARY KEY (`id_reply`,`id_post`,`id_user`),
  KEY `post_id` (`id_post`),
  KEY `author_id` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_post_replies`
--

INSERT INTO `user_post_replies` (`id_reply`, `id_post`, `id_user`, `date_created`, `text`) VALUES
(1, 1, 82, '2016-09-03 15:06:20', 'Ovo je neki Markov odgovor..'),
(2, 1, 83, '2016-09-03 18:02:25', 'Ovo je Radovanov odgovor..');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_pictures`
--

CREATE TABLE IF NOT EXISTS `user_profile_pictures` (
  `picture_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) NOT NULL,
  `url` varchar(200) NOT NULL,
  `filetype` varchar(10) NOT NULL,
  `extension` varchar(7) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `deprecated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`picture_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_reply_approves`
--

CREATE TABLE IF NOT EXISTS `user_reply_approves` (
  `id_reply` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  KEY `response_id` (`id_reply`),
  KEY `user_id` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_reply_approves`
--

INSERT INTO `user_reply_approves` (`id_reply`, `id_user`) VALUES
(1, 82),
(1, 83);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_friends`
--
ALTER TABLE `user_friends`
  ADD CONSTRAINT `user_friends_ibfk_1` FOREIGN KEY (`id_user_one`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_friends_ibfk_2` FOREIGN KEY (`id_user_two`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_group_belongs`
--
ALTER TABLE `user_group_belongs`
  ADD CONSTRAINT `user_group_belongs_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id_group`),
  ADD CONSTRAINT `user_group_belongs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD CONSTRAINT `user_logins_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `user_devices` (`device_id`),
  ADD CONSTRAINT `user_logins_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_posts`
--
ALTER TABLE `user_posts`
  ADD CONSTRAINT `user_posts_ibfk_3` FOREIGN KEY (`id_group`) REFERENCES `user_groups` (`id_group`),
  ADD CONSTRAINT `user_posts_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_posts_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `user_groups` (`id_group`);

--
-- Constraints for table `user_post_files`
--
ALTER TABLE `user_post_files`
  ADD CONSTRAINT `user_post_files_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `user_posts` (`id_post`);

--
-- Constraints for table `user_post_interests`
--
ALTER TABLE `user_post_interests`
  ADD CONSTRAINT `user_post_interests_ibfk_2` FOREIGN KEY (`id_interest`) REFERENCES `user_interests` (`id_interest`),
  ADD CONSTRAINT `user_post_interests_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `user_posts` (`id_post`);

--
-- Constraints for table `user_post_replies`
--
ALTER TABLE `user_post_replies`
  ADD CONSTRAINT `user_post_replies_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `user_posts` (`id_post`),
  ADD CONSTRAINT `user_post_replies_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_profile_pictures`
--
ALTER TABLE `user_profile_pictures`
  ADD CONSTRAINT `user_profile_pictures_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_reply_approves`
--
ALTER TABLE `user_reply_approves`
  ADD CONSTRAINT `user_reply_approves_ibfk_1` FOREIGN KEY (`id_reply`) REFERENCES `user_post_replies` (`id_reply`),
  ADD CONSTRAINT `user_reply_approves_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
