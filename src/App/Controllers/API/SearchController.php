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
use App\Services\SearchService;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{

    protected $seachHandler;

    public function __construct(
        SearchHandler $seachHandler
    ) {
        $this->seachHandler = $seachHandler;
    }

    public function searchTopAction (Application $app, Request $request) {
        $finalUsersPagin = $this->seachHandler
            ->handleSearch($app, $request, 'user');

        $finalGroupsPagin = $this->seachHandler
            ->handleSearch($app, $request, 'group');

        $finalPostPagin = $this->seachHandler
            ->handleSearch($app, $request, 'post');

        $finalResult['data']['users'] = $finalUsersPagin;
        $finalResult['data']['groups'] = $finalGroupsPagin;
        $finalResult['data']['posts'] = $finalPostPagin;

        return new JsonResponse($finalResult, 200);
    }

    public function searchUsersAction (Application $app, Request $request) {
        $finalUsersPagin = $this->seachHandler
            ->handleSearch($app, $request, 'user');

        return new JsonResponse($finalUsersPagin, 200);
    }

    public function searchGroupsAction (Application $app, Request $request) {

        $finalGroupsPagin = $this->seachHandler
            ->handleSearch($app, $request, 'group');

        return new JsonResponse($finalGroupsPagin, 200);
    }

    public function searchPostsAction (Application $app, Request $request) {

        $finalPostsPagin = $this->seachHandler
            ->handleSearch($app, $request, 'post');

        return new JsonResponse($finalPostsPagin, 200);
    }

    public function autocompleteAction (Application $app, Request $request) {
        return $this->seachHandler
            ->autocomplete($app, $request);
    }

}