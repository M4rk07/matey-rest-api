<?php
namespace App\Handlers\Search;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.25
 */
interface SearchHandlerInterface
{
    public function handleSearch (Application $app, Request $request, $type);
}