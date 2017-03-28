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
  first_name varchar(50) CHARACTER SET utf8 NOT NULL,
  last_name varchar(50) CHARACTER SET utf8 NOT NULL,
  full_name varchar(100) CHARACTER SET utf8 NOT NULL,
  country varchar(100) CHARACTER SET utf8,
  location varchar(100) CHARACTER SET utf8,
  birthday DATE,
  phone_number varchar(20) CHARACTER SET utf8,
  is_silhouette boolean NOT NULL DEFAULT 1,
  verified boolean NOT NULL DEFAULT 0,
  date_registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  device_secret varchar(100) NOT NULL,
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
  status boolean NOT NULL DEFAULT 1,
  gcm varchar(500) NOT NULL,
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(device_id) REFERENCES matey_device(device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group`
--

CREATE TABLE IF NOT EXISTS matey_group (
  group_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  group_name varchar(500) NOT NULL,
  description varchar(5000),
  is_silhouette boolean NOT NULL DEFAULT 1,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_post (
  post_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id int(11) UNSIGNED,
  user_id int(11) UNSIGNED NOT NULL,
  title varchar(100) CHARACTER SET utf8,
  text varchar(3000) CHARACTER SET utf8 DEFAULT '',
  attachs_num int(11) NOT NULL DEFAULT 0,
  locations_num int(11) NOT NULL DEFAULT 0,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  archived boolean NOT NULL DEFAULT 0,
  deleted boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (post_id),
  FOREIGN KEY(group_id) REFERENCES matey_group(group_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_reply (
  reply_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  text varchar(3000) CHARACTER SET utf8,
  attachs_num int(11) NOT NULL DEFAULT 0,
  locations_num int(11) NOT NULL DEFAULT 0,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (reply_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_rereply (
  rereply_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  reply_id int(11) UNSIGNED NOT NULL,
  text varchar(3000) CHARACTER SET utf8,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (rereply_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(reply_id) REFERENCES matey_reply(reply_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_group_scope (
  scope varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (scope)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_group_admin (
  user_id int(11) UNSIGNED NOT NULL,
  group_id int(11) UNSIGNED NOT NULL,
  scope varchar(300) CHARACTER SET utf8,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  active boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (user_id, group_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(group_id) REFERENCES matey_group(group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_group_favorite (
  user_id int(11) UNSIGNED NOT NULL,
  group_id int(11) UNSIGNED NOT NULL,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, group_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(group_id) REFERENCES matey_group(group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_share (
  user_id int(11) UNSIGNED NOT NULL,
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(20) CHARACTER SET utf8,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_follow (
  user_id int(11) UNSIGNED NOT NULL,
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(20) CHARACTER SET utf8,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, parent_id, parent_type),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_boost (
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_bookmark (
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_location (
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(20) CHARACTER SET utf8,
  latt varchar(20) CHARACTER SET utf8,
  longt varchar(20) CHARACTER SET utf8,
  description VARCHAR(100) CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity_type`
--

CREATE TABLE IF NOT EXISTS matey_activity_type (
  activity_type varchar(30) NOT NULL,
  PRIMARY KEY (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_object_type`
--

CREATE TABLE IF NOT EXISTS matey_object_type (
  object_type varchar(30) NOT NULL,
  PRIMARY KEY (object_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_admin`
--

CREATE TABLE IF NOT EXISTS matey_approve (
  user_id int(11) UNSIGNED NOT NULL,
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(20) CHARACTER SET utf8,
  time_c TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, parent_id, parent_type),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY(parent_type) REFERENCES matey_object_type(object_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity`
--

CREATE TABLE IF NOT EXISTS matey_activity (
  activity_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  source_id int(11) UNSIGNED NOT NULL,
  source_type varchar(50) NOT NULL,
  parent_id int(11) UNSIGNED,
  parent_type varchar(50),
  activity_type varchar(50) NOT NULL,
  time_c timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (activity_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (activity_type) REFERENCES matey_activity_type(activity_type),
  FOREIGN KEY (parent_type) REFERENCES matey_object_type(object_type),
  FOREIGN KEY (source_type) REFERENCES matey_object_type(object_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_user`
--

CREATE TABLE IF NOT EXISTS oauth2_user (
  user_id int(11) NOT NULL,
  username varchar(50) CHARACTER SET utf8 NOT NULL,
  password varchar(128) CHARACTER SET utf8 NOT NULL,
  salt varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY (username),
  FOREIGN KEY (username) REFERENCES matey_user(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_clients`
--

CREATE TABLE IF NOT EXISTS oauth2_client (
  client_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  client_secret varchar(255) NOT NULL,
  app_name varchar(100) NOT NULL,
  redirect_uri text,
  registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  client_type varchar(12) NOT NULL DEFAULT 'public',
  PRIMARY KEY (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_access_tokens`
--

CREATE TABLE IF NOT EXISTS oauth2_access_token (
  access_token varchar(32) NOT NULL,
  token_type varchar(20) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, access_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id)
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
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id),
  FOREIGN KEY (username) REFERENCES oauth2_user(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `oauth2_codes`
--

CREATE TABLE IF NOT EXISTS oauth2_code (
  code varchar(34) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  redirect_uri text,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, code),
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oauth2_codes`
--

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_refresh_tokens`
--

CREATE TABLE IF NOT EXISTS oauth2_refresh_token (
  refresh_token varchar(32) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, refresh_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_scopes`
--

CREATE TABLE IF NOT EXISTS oauth2_scope (
  scope varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (scope)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO matey_activity_type (activity_type) VALUES
('FOLLOW'), ('SHARE'), ('BOOKMARK'), ('BOOST'), ('APPROVE'), ('ARCHIVE'),
('REPLY_CREATE'), ('REREPLY_CREATE'), ('GROUP_CREATE'), ('POST_CREATE');

INSERT INTO matey_object_type (object_type) VALUES
('MATEY_USER'), ('GROUP'), ('POST'), ('REPLY'), ('REREPLY');

-- --------------------------------------------------------

--
-- POPULATE table `oauth2_client`
--

INSERT INTO oauth2_client (app_name) VALUES ('Matey');

ALTER TABLE matey_group AUTO_INCREMENT=1001;
ALTER TABLE matey_user AUTO_INCREMENT=1001;
ALTER TABLE matey_post AUTO_INCREMENT=1001;
ALTER TABLE matey_reply AUTO_INCREMENT=1001;
ALTER TABLE matey_rereply AUTO_INCREMENT=1001;
ALTER TABLE matey_activity AUTO_INCREMENT=1001;
ALTER TABLE matey_device AUTO_INCREMENT=1001;