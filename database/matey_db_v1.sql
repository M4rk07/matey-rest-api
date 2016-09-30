-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 07:39 PM
-- Server version: 5.7.15-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `matey_db_v1`
--

--
-- Table structure for table `matey_user`
--

CREATE TABLE IF NOT EXISTS matey_user (
  id_user int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(50) CHARACTER SET utf8 NOT NULL,
  first_name varchar(50) CHARACTER SET utf8 NOT NULL,
  last_name varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY(id_user),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_standard_user`
--

CREATE TABLE IF NOT EXISTS matey_standard_user (
  id_user int(11) UNSIGNED NOT NULL,
  password varchar(40) NOT NULL,
  salt varchar(20) NOT NULL,
  PRIMARY KEY(id_user),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_fb_user`
--

CREATE TABLE IF NOT EXISTS matey_fb_user (
  id_user int(11) UNSIGNED NOT NULL,
  fb_id bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY(id_user),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_follower`
--

CREATE TABLE IF NOT EXISTS matey_follower (
  from_user int(11) UNSIGNED NOT NULL,
  to_user int(11) UNSIGNED NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (from_user, to_user),
  FOREIGN KEY (from_user) REFERENCES matey_user(id_user),
  FOREIGN KEY (to_user) REFERENCES matey_user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_post`
--

CREATE TABLE IF NOT EXISTS matey_post (
  id_post int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user int(11) UNSIGNED NOT NULL,
  text varchar(7000) NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  num_of_responses int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (id_post),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_response`
--

CREATE TABLE IF NOT EXISTS matey_response (
  id_response int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user int(11) UNSIGNED NOT NULL,
  id_post int(11) UNSIGNED NOT NULL,
  text varchar(7000) NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  num_of_approves int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (id_response),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user),
  FOREIGN KEY (id_post) REFERENCES matey_post(id_post)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_approve`
--

CREATE TABLE IF NOT EXISTS matey_approve (
  id_user int(11) UNSIGNED NOT NULL,
  id_response int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (id_user, id_response),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user),
  FOREIGN KEY (id_response) REFERENCES matey_response(id_response)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_share`
--

CREATE TABLE IF NOT EXISTS matey_share (
  id_user int(11) UNSIGNED NOT NULL,
  id_post int(11) UNSIGNED NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user),
  FOREIGN KEY (id_post) REFERENCES matey_post(id_post)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity`
--

CREATE TABLE IF NOT EXISTS matey_activity (
  id_activity int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user int(11) UNSIGNED NOT NULL,
  id_source int(11) UNSIGNED NOT NULL,
  activity_type varchar(50) NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  data blob NOT NULL,
  PRIMARY KEY (id_activity),
  FOREIGN KEY (id_user) REFERENCES matey_user(id_user)
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


