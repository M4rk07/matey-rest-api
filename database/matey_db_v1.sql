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
  device_secret varchar(100),
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
  post_id varchar(50) NOT NULL,
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
  post_id varchar(50) NOT NULL,
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
  response_id varchar(50) NOT NULL,
  user_id int(11) UNSIGNED NOT NULL,
  post_id varchar(50) NOT NULL,
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
  response_id varchar(50) NOT NULL,
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
  post_id varchar(50) NOT NULL,
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
  source_id varchar(50) NOT NULL,
  parent_id varchar(50) NOT NULL,
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
  interest varchar(50) NOT NULL,
  PRIMARY KEY (interest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_subinterest`
--

CREATE TABLE IF NOT EXISTS matey_subinterest (
  interest varchar(50) NOT NULL,
  subinterest varchar(50) NOT NULL,
  PRIMARY KEY (subinterest),
  FOREIGN KEY(interest) REFERENCES matey_interest(interest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matey_user`
--

CREATE TABLE IF NOT EXISTS oauth2_user (
  username varchar(50) CHARACTER SET utf8 NOT NULL,
  password varchar(128) CHARACTER SET utf8 NOT NULL,
  salt varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (username),
  KEY (username, password, salt),
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

INSERT INTO matey_subinterest (interest, subinterest) VALUES
('Arts & Entertainment', 'Celebrities & Entertainment News'),
('Arts & Entertainment', 'Comics & Animation'),
('Arts & Entertainment', 'Humor'),
('Arts & Entertainment', 'Movies'),
('Arts & Entertainment', 'Music & Audio'),
('Arts & Entertainment', 'TV'),
('Automotive & Vehicle', 'Boats & Watercraft'),
('Automotive & Vehicle', 'Classic Vehicles'),
('Automotive & Vehicle', 'Motorcycles'),
('Beauty & Fitness', 'Face & Body Care'),
('Beauty & Fitness', 'Fashion & Style'),
('Beauty & Fitness', 'Fitness'),
('Business & Industrial', 'Advertising & Marketing'),
('Business & Industrial', 'Finance'),
('Business & Industrial', 'Business News'),
('Business & Industrial', 'Business Services'),
('Computers & Technology', 'Computer Hardware'),
('Computers & Technology', 'Consumer Electronics'),
('Computers & Technology', 'Programming'),
('Computers & Technology', 'News'),
('Education and Employment', 'Colleges & Universities'),
('Education and Employment', 'Employment'),
('Education and Employment', 'Primary & Secondary Schooling (K-12)'),
('Food & Drink', 'Cooking & Recipes'),
('Food & Drink', 'Restaurants'),
('Home & Garden', 'Gardening & Landscaping'),
('Home & Garden', 'Home Furnishings'),
('Home & Garden', 'Home Improvement'),
('Home & Garden', 'Real Estate Listings'),
('Law & Government', 'Government'),
('Law & Government', 'Legal'),
('Leisure & Hobbies', 'Books'),
('Leisure & Hobbies', 'Crafts'),
('Leisure & Hobbies', 'Games & Puzzles'),
('Leisure & Hobbies', 'Outdoors'),
('News', 'Politics'),
('News', 'Weather'),
('News', 'Global News'),
('Science', 'Math'),
('Science', 'Physics'),
('Shopping', 'Apparel'),
('Shopping', 'Discount & Outlet Stores'),
('Shopping', 'Toys'),
('Sports', 'College Sports'),
('Sports', 'Extreme Sports'),
('Sports', 'Fantasy Sports'),
('Sports', 'Professional Sports'),
('Sports', 'Sports News'),
('Travel', 'Hotels & Transportation'),
('Travel', 'Tourist Destinations'),
('Travel', 'Travel Guides & Travelogues'),
('Video Games', 'Casual'),
('Video Games', 'Hardcore'),
('Video Games', 'Virtual Worlds');

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