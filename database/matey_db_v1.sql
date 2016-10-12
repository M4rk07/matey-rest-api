-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 07:39 PM
-- Server version: 5.7.15-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET GLOBAL time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `matey_db_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `matey_user`
--

CREATE TABLE IF NOT EXISTS matey_user (
  user_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(50) CHARACTER SET utf8 NOT NULL,
  verified tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  is_active tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  first_name varchar(50) CHARACTER SET utf8 NOT NULL,
  last_name varchar(50) CHARACTER SET utf8 NOT NULL,
  birth_year int(11) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_user`
--

CREATE TABLE IF NOT EXISTS matey_facebook_info (
  user_id int(11) UNSIGNED NOT NULL,
  fb_id bigint(64) UNSIGNED NOT NULL,
  PRIMARY KEY(user_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_device`
--

CREATE TABLE IF NOT EXISTS matey_device (
  device_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  time_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  gcm varchar(500) NOT NULL,
  PRIMARY KEY(device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_login`
--

CREATE TABLE IF NOT EXISTS matey_login (
  user_id int(11) UNSIGNED NOT NULL,
  device_id int(11) UNSIGNED NOT NULL,
  time_logged TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (device_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(device_id) REFERENCES matey_device(device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_follower`
--

CREATE TABLE IF NOT EXISTS matey_follower (
  from_user int(11) UNSIGNED NOT NULL,
  to_user int(11) UNSIGNED NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (from_user, to_user),
  FOREIGN KEY (from_user) REFERENCES matey_user(user_id),
  FOREIGN KEY (to_user) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_post`
--

CREATE TABLE IF NOT EXISTS matey_post (
  post_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  text varchar(7000) NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  num_of_responses int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (post_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_response`
--

CREATE TABLE IF NOT EXISTS matey_response (
  response_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  text varchar(7000) NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  num_of_approves int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (response_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_approve`
--

CREATE TABLE IF NOT EXISTS matey_approve (
  user_id int(11) UNSIGNED NOT NULL,
  response_id int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, response_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (response_id) REFERENCES matey_response(response_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_share`
--

CREATE TABLE IF NOT EXISTS matey_share (
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity_type`
--

CREATE TABLE IF NOT EXISTS matey_activity_type (
  activity_type varchar(50) NOT NULL,
  PRIMARY KEY (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_parent_type`
--

CREATE TABLE IF NOT EXISTS matey_parent_type (
  parent_type varchar(50) NOT NULL,
  PRIMARY KEY (parent_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity`
--

CREATE TABLE IF NOT EXISTS matey_activity (
  activity_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  source_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(50) NOT NULL,
  activity_type varchar(50) NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  srl_data blob NOT NULL,
  PRIMARY KEY (activity_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (activity_type) REFERENCES matey_activity_type(activity_type),
  FOREIGN KEY (parent_type) REFERENCES matey_parent_type(parent_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



