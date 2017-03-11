<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.11
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Group\GroupHandlerFactoryInterface;
use App\Handlers\Group\GroupHandlerInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupController extends AbstractController
{

    protected $groupHandler;

    public function __construct(
        GroupHandlerInterface $groupHandler
    ) {
        $this->groupHandler = $groupHandler;
    }

    public function createGroupAction (Application $app, Request $request) {
        return $this->groupHandler
            ->createGroup($app, $request);
    }

    public function getGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandler
            ->getGroup($app, $request, $groupId);
    }

    public function deleteGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandler
            ->deleteGroup($app, $request, $groupId);
    }

}