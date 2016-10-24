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
  profile_picture VARCHAR(1000) NOT NULL DEFAULT 'http://image.flaticon.com/icons/png/128/149/149071.png',
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
  PRIMARY KEY (post_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_bookmark`
--

CREATE TABLE IF NOT EXISTS matey_bookmark (
  post_id int(11) UNSIGNED NOT NULL,
  user_id int(11) UNSIGNED NOT NULL,
  clock timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
-- Table structure for table `matey_activity`
--

CREATE TABLE IF NOT EXISTS matey_activity (
  activity_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL,
  source_id int(11) UNSIGNED NOT NULL,
  parent_id int(11) UNSIGNED NOT NULL,
  parent_type varchar(50) NOT NULL,
  activity_type varchar(50) NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  srl_data blob NOT NULL,
  PRIMARY KEY (activity_id),
  FOREIGN KEY (user_id) REFERENCES matey_user(user_id),
  FOREIGN KEY (activity_type) REFERENCES matey_activity_type(activity_type),
  FOREIGN KEY (parent_type) REFERENCES matey_activity_type(activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_interest`
--

CREATE TABLE IF NOT EXISTS matey_interest (
  interest_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_subinterest`
--

CREATE TABLE IF NOT EXISTS matey_subinterest (
  subinterest_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  interest_id int(11) UNSIGNED NOT NULL,
  subinterest varchar(50) NOT NULL,
  PRIMARY KEY (subinterest_id),
  FOREIGN KEY(interest_id) REFERENCES matey_interest(interest_id)
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
  scope varchar(20) NOT NULL,
  PRIMARY KEY (scope)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- POPULATE table `oauth2_client`
--

INSERT INTO oauth2_client (app_name) VALUES ('Matey');

-- --------------------------------------------------------

--
-- POPULATE table `matey_interest`
--

INSERT INTO matey_interest (interest) VALUES
('Arts & Entertainment'),
('Automotive & Vehicle'),
('Beauty & Fitness'),
('Business & Industrial'),
('Computers & Technology'),
('Education and Employment'),
('Food & Drink'),
('Home & Garden'),
('Law & Government'),
('Leisure & Hobbies'),
('News'),
('Science'),
('Shopping'),
('Sports'),
('Travel'),
('Video Games');

-- --------------------------------------------------------

--
-- POPULATE table `matey_subinterest`
--

INSERT INTO matey_subinterest (interest_id, subinterest) VALUES
(1, 'Celebrities & Entertainment News'),
(1, 'Comics & Animation'),
(1, 'Humor'),
(1, 'Movies'),
(1, 'Music & Audio'),
(1, 'TV'),
(2, 'Boats & Watercraft'),
(2, 'Classic Vehicles'),
(2, 'Motorcycles'),
(3, 'Face & Body Care'),
(3, 'Fashion & Style'),
(3, 'Fitness'),
(4, 'Advertising & Marketing'),
(4, 'Finance'),
(4, 'Business News'),
(4, 'Business Services'),
(5, 'Computer Hardware'),
(5, 'Consumer Electronics'),
(5, 'Programming'),
(5, 'News'),
(6, 'Colleges & Universities'),
(6, 'Employment'),
(6, 'Primary & Secondary Schooling (K-12)'),
(7, 'Cooking & Recipes'),
(7, 'Restaurants'),
(8, 'Gardening & Landscaping'),
(8, 'Home Furnishings'),
(8, 'Home Improvement'),
(8, 'Real Estate Listings'),
(9, 'Government'),
(9, 'Legal'),
(10, 'Books'),
(10, 'Crafts'),
(10, 'Games & Puzzles'),
(10, 'Outdoors'),
(11, 'Politics'),
(11, 'Weather'),
(11, 'Global News'),
(12, 'Math'),
(12, 'Physics'),
(13, 'Apparel'),
(13, 'Discount & Outlet Stores'),
(13, 'Toys'),
(14, 'College Sports'),
(14, 'Extreme Sports'),
(14, 'Fantasy Sports'),
(14, 'Professional Sports'),
(14, 'Sports News'),
(15, 'Hotels & Transportation'),
(15, 'Tourist Destinations'),
(15, 'Travel Guides & Travelogues'),
(16, 'Casual'),
(16, 'Hardcore'),
(16, 'Virtual Worlds');

-- --------------------------------------------------------

--
-- POPULATE table `matey_activity_type`
--

INSERT INTO matey_activity_type (activity_type) VALUES
('INTEREST'), ('POST'), ('RESPONSE'),
('FOLLOW'), ('SHARE');

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