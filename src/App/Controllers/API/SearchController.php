<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.42
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Search\SearchHandler;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{

    protected $seachHandler;

    public function __construct(
        SearchHandler $seachHandler
    ) {
        $this->seachHandler = $seachHandler;
    }

    public function searchAction (Application $app, Request $request) {
        return $this->seachHandler
            ->handleSearch($request);
    }

}