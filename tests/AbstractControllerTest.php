<?php
namespace App\Tests\Controllers;
use App\Paths\Paths;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.3.17.
 * Time: 01.09
 */
abstract class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{

    protected $client;

    protected function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost/matey-api/web/index.php/',
            'http_errors' => false
        ]);
    }

}