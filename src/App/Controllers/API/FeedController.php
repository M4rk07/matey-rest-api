<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.3.17.
 * Time: 18.28
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Feed\FeedHandlerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class FeedController extends AbstractController
{

    protected $feedHandler;

    public function __construct(
        FeedHandlerInterface $feedHandler
    ) {
        $this->feedHandler = $feedHandler;
    }

    public function getFeedAction(Application $app, Request $request) {
        return $this->feedHandler
            ->getFeed($app, $request);
    }

}