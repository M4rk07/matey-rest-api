-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 07:39 PM
-- Server version: 5.7.15-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET GLOBAL time_zone = "Europe/Belgrade";


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
  password varchar(128) NOT NULL,
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
-- Table structure for table `oauth2_clients`
--

CREATE TABLE IF NOT EXISTS oauth2_clients (
  client_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  client_secret varchar(255) NOT NULL,
  app_name varchar(100) NOT NULL,
  redirect_uri text,
  registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  client_type varchar(12) NOT NULL,
  PRIMARY KEY (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_access_tokens`
--

CREATE TABLE IF NOT EXISTS oauth2_access_tokens (
  access_token varchar(32) NOT NULL,
  token_type varchar(20) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, access_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_clients(client_id),
  FOREIGN KEY (username) REFERENCES matey_user(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_authorize`
--

CREATE TABLE IF NOT EXISTS oauth2_authorize (
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  scope text,
  authorization_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (client_id, username),
  FOREIGN KEY (client_id) REFERENCES oauth2_clients(client_id),
  FOREIGN KEY (username) REFERENCES matey_user(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `oauth2_codes`
--

CREATE TABLE IF NOT EXISTS oauth2_codes (
  code varchar(34) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  redirect_uri text,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, code),
  FOREIGN KEY (client_id) REFERENCES oauth2_clients(client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oauth2_codes`
--

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_refresh_tokens`
--

CREATE TABLE IF NOT EXISTS oauth2_refresh_tokens (
  refresh_token varchar(32) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, refresh_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_clients(client_id),
  FOREIGN KEY (username) REFERENCES matey_user(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_scopes`
--

CREATE TABLE IF NOT EXISTS oauth2_scopes (
  scope varchar(20) NOT NULL,
  PRIMARY KEY (scope)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_device`
--

CREATE TABLE IF NOT EXISTS matey_device (
  id_device int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  time_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id_device)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_login`
--

CREATE TABLE IF NOT EXISTS matey_login (
  id_user int(11) UNSIGNED NOT NULL,
  id_device int(11) UNSIGNED NOT NULL,
  time_logged TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_user, id_device),
  FOREIGN KEY(id_user) REFERENCES matey_user(id_user),
  FOREIGN KEY(id_device) REFERENCES matey_device(id_device)
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
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  srl_data blob NOT NULL,
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


