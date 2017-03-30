<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.11
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\AbstractHandler;
use App\Handlers\Group\GroupHandlerFactoryInterface;
use App\Handlers\Group\GroupHandlerInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            ->handleCreateGroup($app, $request);
    }

    public function getGroupAction (Application $app, Request $request, $groupId) {

        $groupResult = $this->groupHandler
            ->handleGetGroup($request, $groupId);

        $finalResult['data'] = $groupResult;

        return new JsonResponse($finalResult, 200);
    }

    public function deleteGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandler
            ->handleDeleteGroup($request, $groupId);
    }

    public function getFollowingGroupsAction (Application $app, Request $request, $userId) {
        if($userId == 'me') $userId = AbstractHandler::getTokenUserId($request);

        return $this->groupHandler
            ->handleGetFollowingGroups($request, $userId);
    }

    public function followGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandler
            ->handleFollowGroup($request, $groupId);
    }

    public function uploadGroupPictureAction (Application $app, Request $request, $groupId) {
        $fileHandler = $app['matey.file_handler.factory'];
        return $fileHandler
            ->getFileHandler('group_picture')
            ->upload($app, $request, $groupId);
    }

}