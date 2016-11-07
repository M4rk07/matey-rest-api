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
  full_name varchar(100) CHARACTER SET utf8 NOT NULL,
  is_silhouette tinyint(1) NOT NULL DEFAULT 1,
  first_login tinyint(1) NOT NULL DEFAULT 0,
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
-- Table structure for table `matey_newsfeed`
--

CREATE TABLE IF NOT EXISTS matey_newsfeed (
  user_id int(11) UNSIGNED NOT NULL,
  feed_name varchar(200) NOT NULL,
  date_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, feed_name)
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
  status int(11) NOT NULL DEFAULT 1,
  gcm varchar(500) NOT NULL,
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
  date_started timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  date_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (post_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_share`
--

CREATE TABLE IF NOT EXISTS matey_share (
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  date_shared timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (post_id) REFERENCES matey_post(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_post_follow`
--

CREATE TABLE IF NOT EXISTS matey_post_follow (
  user_id int(11) UNSIGNED NOT NULL,
  post_id int(11) UNSIGNED NOT NULL,
  date_started timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (post_id) REFERENCES matey_post(post_id)
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
  date_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted tinyint(1) NOT NULL DEFAULT 0,
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
-- Table structure for table `matey_activity_type`
--

CREATE TABLE IF NOT EXISTS matey_activity_type (
  activity_type varchar(50) NOT NULL,
  PRIMARY KEY (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_activity`
--

CREATE TABLE IF NOT EXISTS matey_activity (
  activity_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  source_id int(11) UNSIGNED NOT NULL,
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(50) NOT NULL,
  activity_type varchar(50) NOT NULL,
  activity_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  srl_data blob NOT NULL,
  deleted tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (activity_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (activity_type) REFERENCES matey_activity_type(activity_type),
  FOREIGN KEY (parent_type) REFERENCES matey_activity_type(activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_user_interest`
--

CREATE TABLE IF NOT EXISTS matey_user_interest (
  user_id int(11) UNSIGNED NOT NULL,
  interest_id int(11) UNSIGNED NOT NULL,
  depth tinyint(1) NOT NULL,
  PRIMARY KEY (user_id, interest_id, depth),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_interest_depth_0`
--

CREATE TABLE IF NOT EXISTS matey_interest_depth_0 (
  interest_0_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest_0_id),
  UNIQUE KEY (interest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_interest_depth_1`
--

CREATE TABLE IF NOT EXISTS matey_interest_depth_1 (
  interest_1_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest_0_id int(11) UNSIGNED NOT NULL,
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest_1_id),
  UNIQUE KEY (interest),
  FOREIGN KEY (interest_0_id) REFERENCES matey_interest_depth_0(interest_0_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_interest_depth_2`
--

CREATE TABLE IF NOT EXISTS matey_interest_depth_2 (
  interest_2_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest_1_id int(11) UNSIGNED NOT NULL,
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest_2_id),
  UNIQUE KEY (interest),
  FOREIGN KEY (interest_1_id) REFERENCES matey_interest_depth_1(interest_1_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_interest_depth_3`
--

CREATE TABLE IF NOT EXISTS matey_interest_depth_3 (
  interest_3_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest_2_id int(11) UNSIGNED NOT NULL,
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest_3_id),
  UNIQUE KEY (interest),
  FOREIGN KEY (interest_2_id) REFERENCES matey_interest_depth_2(interest_2_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group`
--

CREATE TABLE IF NOT EXISTS matey_group (
  group_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  group_name varchar(500) NOT NULL,
  description varchar(5000),
  PRIMARY KEY (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_group_follow`
--

CREATE TABLE IF NOT EXISTS matey_group_follow (
  group_id int(11) UNSIGNED NOT NULL,
  user_id int(11) UNSIGNED NOT NULL,
  date_started TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (group_id, user_id),
  FOREIGN KEY(group_id) REFERENCES matey_group(group_id),
  FOREIGN KEY(user_id) REFERENCES matey_user(user_id)
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

-- --------------------------------------------------------

--
-- POPULATE table `oauth2_client`
--

INSERT INTO oauth2_client (app_name) VALUES ('Matey');

-- --------------------------------------------------------

--
-- POPULATE table `matey_activity_type`
--

INSERT INTO matey_activity_type (activity_type) VALUES
('INTEREST'), ('POST'), ('RESPONSE'),
('FOLLOW'), ('SHARE'), ('GENERAL');

-- --------------------------------------------------------

--
-- TRIGGER for table `matey_posts`
--

DELIMITER /
CREATE TRIGGER delete_response AFTER DELETE on matey_post
FOR EACH ROW
BEGIN
DELETE FROM matey_response
WHERE matey_response.post_id = old.post_id;
END;
/
DELIMITER ;
-- --------------------------------------------------------

--
-- TRIGGER for table `matey_responses`
--
DELIMITER /
CREATE TRIGGER delete_approve AFTER DELETE on matey_response
FOR EACH ROW
BEGIN
DELETE FROM matey_approve
WHERE matey_approve.response_id = old.response_id;
END;
/
DELIMITER ;