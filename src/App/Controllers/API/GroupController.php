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
            ->handleCreateGroup($request);
    }

    public function getGroupAction (Application $app, Request $request, $groupId) {

        $groupResult = $this->groupHandler
            ->handleGetGroup($request, $groupId);

        $finalResult['data'] = $groupResult;

        $postController = $app['matey.post_controller'];
        $deckResult = $postController->handleGetDeck($app, $request, $groupId);
        if($deckResult->getStatusCode() !== 200) return $deckResult;
        $deckResult = json_decode($deckResult->getContent());

        $finalResult['posts'] = $deckResult;

        return new JsonResponse($finalResult, 200);
    }

    public function deleteGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandler
            ->handleDeleteGroup($request, $groupId);
    }

}