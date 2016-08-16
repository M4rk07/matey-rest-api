-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2016 at 09:55 PM
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
-- Table structure for table `belongs`
--

CREATE TABLE IF NOT EXISTS `belongs` (
  `group_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `confirms`
--

CREATE TABLE IF NOT EXISTS `confirms` (
  `response_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  KEY `response_id` (`response_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `device_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_got_live` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `board` varchar(30) DEFAULT NULL,
  `brand` varchar(30) DEFAULT NULL,
  `device` varchar(30) DEFAULT NULL,
  `model` varchar(30) DEFAULT NULL,
  `gcm_token` varchar(500) NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- Table structure for table `fb_access_tokens`
--

CREATE TABLE IF NOT EXISTS `fb_access_tokens` (
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
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
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
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `user_one_id` bigint(20) NOT NULL,
  `user_two_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `date_friends` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `user_one_id_2` (`user_one_id`),
  KEY `user_two_id` (`user_two_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator_id` bigint(20) NOT NULL,
  `group_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `creator_id` (`creator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Triggers `groups`
--
DROP TRIGGER IF EXISTS `automatic_join`;
DELIMITER //
CREATE TRIGGER `automatic_join` AFTER INSERT ON `groups`
 FOR EACH ROW INSERT INTO belongs (group_id, user_id) VALUES (NEW.group_id, NEW.creator_id)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `device_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `last_log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_logged` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `device_id` (`device_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_clients`
--

CREATE TABLE IF NOT EXISTS `oauth2_clients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` varchar(255) DEFAULT NULL,
  `redirect_uri` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phone_verifications`
--

CREATE TABLE IF NOT EXISTS `phone_verifications` (
  `user_id` bigint(20) NOT NULL,
  `code` varchar(5) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile_pictures`
--

CREATE TABLE IF NOT EXISTS `profile_pictures` (
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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fb_id` bigint(20) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `roles` text,
  `phone_number` varchar(20) DEFAULT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `profile_picture_link` varchar(200) NOT NULL DEFAULT '0',
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` int(11) NOT NULL DEFAULT '0',
  `gender` varchar(7) NOT NULL,
  `birthday` varchar(11) NOT NULL,
  `hometown` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `quote_status` varchar(120) NOT NULL,
  `private` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `generate_activation_link`;
DELIMITER //
CREATE TRIGGER `generate_activation_link` AFTER INSERT ON `users`
 FOR EACH ROW INSERT INTO activation_links (link_id, user_id) VALUES (UUID(), new.user_id)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_posts`
--

CREATE TABLE IF NOT EXISTS `user_posts` (
  `post_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) DEFAULT NULL,
  `text` varchar(320) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_exist` tinyint(1) NOT NULL DEFAULT '0',
  `group_exist` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`),
  KEY `author_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=205 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_responses`
--

CREATE TABLE IF NOT EXISTS `user_responses` (
  `response_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `author_id` bigint(20) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` varchar(320) NOT NULL,
  `map_lat` double DEFAULT NULL,
  `map_lng` double DEFAULT NULL,
  PRIMARY KEY (`response_id`,`post_id`,`author_id`),
  KEY `post_id` (`post_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `belongs`
--
ALTER TABLE `belongs`
  ADD CONSTRAINT `belongs_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `belongs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `confirms`
--
ALTER TABLE `confirms`
  ADD CONSTRAINT `confirms_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `user_responses` (`response_id`),
  ADD CONSTRAINT `confirms_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `user_posts` (`post_id`);

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `logins`
--
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`),
  ADD CONSTRAINT `logins_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `profile_pictures`
--
ALTER TABLE `profile_pictures`
  ADD CONSTRAINT `profile_pictures_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_posts`
--
ALTER TABLE `user_posts`
  ADD CONSTRAINT `user_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_posts_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

--
-- Constraints for table `user_responses`
--
ALTER TABLE `user_responses`
  ADD CONSTRAINT `user_responses_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `user_posts` (`post_id`),
  ADD CONSTRAINT `user_responses_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
