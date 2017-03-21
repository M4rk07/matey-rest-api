<?php
namespace App\Paths;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.10.16.
 * Time: 13.35
 */
class Paths
{
    const BASE_IP = '127.0.0.1';
    const SPHINX_PORT = 9312;
    const SPHINXQL_PORT = 9306;
    const REDIS_PORT = 6379;
    const MYSQL_PORT = 3306;
    const BASE_API_URL = "http://localhost/matey-api/web/index.php";
    const DEBUG_ENDPOINT = "http://localhost/matey-oauth2/web/index.php/api/oauth2/debug";

    const STORAGE_BASE = "https://s3-us-west-2.amazonaws.com";
    const BUCKET_MATEY = "matey";

    const API_ENDPOINT = "/api";
    const API_VERSION = "v1";

}