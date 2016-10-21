<?php
/**
 * Created by PhpStorm.
 * User: Marko
 * Date: 1/28/2016
 * Time: 4:44 PM
 */
use Doctrine\DBAL\DriverManager;

$config = new \Doctrine\DBAL\Configuration();

$connectionParams = array(
    'dbname' => 'matey_db_v1',
    'user' => 'root',
    'password' => 'maka',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
    'charset' => 'utf8',
    'driverOptions' => array(
        1002=>'SET NAMES utf8'
    )
);

/*$connectionParams = array(
    'dbname' => 'matey_db_v1',
    'user' => 'm4rk07',
    'password' => '',
    'host' => '0.0.0.0',
    'driver' => 'pdo_mysql',
    'port' => 3306,
    'charset' => 'utf8',
    'driverOptions' => array(
        1002=>'SET NAMES utf8'
    )
);*/


$conn = DriverManager::getConnection($connectionParams, $config);

return $conn;