<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.45
 */

namespace App\Handlers\Group;


use App\Constants\Defaults\DefaultNumbers;
use App\Exception\NotFoundException;
use App\MateyModels\Activity;
use App\Paths\Paths;
use App\Services\PaginationService;
use App\Validators\GroupId;
use App\Validators\GroupPrivacy;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\UnauthorizedClientException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupHandler extends AbstractGroupHandler
{
    function handleCreateGroup(Application $app, Request $request)
    {
        $userId = self::getTokenUserId($request);

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['group_name'] = $this->gValidateGroupName($jsonDataRequest);
        $jsonData['description'] = $this->gValidateDescription($jsonDataRequest);

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupAdminManager = $this->modelManagerFactory->getModelManager('groupAdmin');
        $group = $groupManager->getModel();
        $groupAdmin = $groupAdminManager->getModel();

        $group->setUserId($userId)
            ->setGroupName($jsonData['group_name'])
            ->setDescription($jsonData['description'])
            ->setSilhouette(1);
        $group = $groupManager->createModel($group);
        $this->createActivity($userId, $group->getGroupId(), Activity::GROUP_TYPE, null, null, Activity::GROUP_CREATE_ACT);
        $app['matey.search_service']->addGroupToSearch($group);

        $groupAdmin->setUserId($userId)
            ->setGroupId($group->getGroupId());
        $groupAdminManager->createModel($groupAdmin);

        $this->handleFollowGroup($request, $group->getGroupId());

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('group_picture')->upload($app, $request, $group->getGroupId());
        }

        $group = $groupManager->readModelOneBy(array(
            'group_id' => $group->getGroupId()
        ));
        $finalResult['data'] = $group->asArray();

        return new JsonResponse($finalResult, 200);
    }

    function handleGetGroup(Request $request, $groupId)
    {
        $userId = self::getTokenUserId($request);
        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->readModelOneBy(array(
            'group_id' => $groupId
        ));

        if(empty($group)) throw new NotFoundException();

        $arrModel = $group->asArray(array_diff($groupManager->getAllFields(), array('deleted')));
        $arrModel['followed'] = $this->isFollowing($userId, $group->getGroupId());

        return $arrModel;
    }

    function handleDeleteGroup(Request $request, $groupId)
    {
        $userId = self::getTokenUserId($request);

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group =$groupManager->getModel();
        $group->setDeleted(1);

        $groupManager->updateModel($group, array(
            'group_id' => $groupId,
            'user_id' => $userId
        ));

        return new JsonResponse(null, 200);

    }

    public function handleFollowGroup(Request $request, $groupId)
    {
        $userId = self::getTokenUserId($request);
        $method = $request->getMethod();

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $follow = $followManager->getModel();
        $group = $groupManager->getModel();

        $follow->setUserId($userId)
            ->setParentId($groupId)
            ->setParentType(Activity::GROUP_TYPE);

        $group->setGroupId($groupId);

        if($method == "PUT") {
            $followManager->createModel($follow);
            $groupManager->incrNumOfFollowers($group);
        } else if($method == "DELETE") {
            $result = $followManager->deleteModel($follow);
            if($result !== null) {
                $groupManager->incrNumOfFollowers($group, -1);
            }
        }

        return new JsonResponse(null, 200);
    }

    public function handleShareGroup(Application $app, Request $request, $groupId) {
        $userId = self::getTokenUserId($request);

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $shareManager = $this->modelManagerFactory->getModelManager('share');
        $share = $shareManager->getModel();

        $share->setUserId($userId)
            ->setParentId($groupId)
            ->setParentType(Activity::GROUP_TYPE);

        $shareManager->createModel($share);

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupManager->incrNumOfShares();

        return new JsonResponse(null, 200);

    }

    public function handleFavoriteGroup (Application $app, Request $request, $groupId) {
        $userId = self::getTokenUserId($request);

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $favoriteManager = $this->modelManagerFactory->getModelManager('favorite');
        $favorite = $favoriteManager->getModel();

        $favorite->setUserId($userId)
            ->setGroupId($groupId);

        $favoriteManager->createModel($favorite);

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupManager->incrNumOfFavorites();

        return new JsonResponse(null, 200);
    }

    public function handleGetFollowingGroups (Request $request, $userId) {
        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        $criteria['user_id'] = $userId;
        $criteria['parent_type'] = Activity::GROUP_TYPE;

        if(!empty($pagParams['max_id'])) $criteria['parent_id:<'] = $pagParams['max_id'];

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follows = $followManager->readModelBy($criteria, array('parent_id' => 'DESC'), $pagParams['count']);

        $groupResult = array();

        if(!empty($follows)) {
            $groupIds = array();
            foreach ($follows as $follow) {
                $groupIds[] = $follow->getParentId();
            }

            $groupManager = $this->modelManagerFactory->getModelManager('group');
            $groups = $groupManager->readModelBy(array(
                'group_id' => array_unique($groupIds)
            ), array('group_id' => 'DESC'), $pagParams['count'], null, array('group_id', 'group_name', 'num_of_followers'));

            foreach ($groups as $group) {
                $groupResult[] = $group->asArray();
            }
        }

        $paginationService = new PaginationService($groupResult, $pagParams['count'],
            '/users/'.$userId.'/groups/following', 'group_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function isFollowing($userId, $followingId) {

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follow = $followManager->readModelOneBy(array(
            'user_id' => $userId,
            'parent_id' => $followingId,
            'parent_type' => Activity::GROUP_TYPE
        ));

        if(!empty($follow)) return true;
        return false;

    }

}