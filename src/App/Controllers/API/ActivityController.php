<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 23.3.17.
 * Time: 20.01
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Activity\ActivityInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends AbstractController
{

    protected $activityHandler;

    public function __construct(
        ActivityInterface $activityHandler
    ) {
        $this->activityHandler = $activityHandler;
    }

    public function getNotificationsAction (Application $app, Request $request) {
        return $this->activityHandler
            ->getNotifications($request);
    }

    public function getActivitiesAction(Application $app, Request $request, $userId) {
        return $this->activityHandler
            ->getActivities($request, $userId);
    }

}