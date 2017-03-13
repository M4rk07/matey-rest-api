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
    function handleCreateGroup(Request $request)
    {
        $userId = $request->request->get('user_id');

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');

        $this->validateNumOfFiles($request);
        $jsonDataRequest = $this->getJsonPostData($request, $contentType);

        $jsonData = array();
        $jsonData['group_name'] = $this->gValidateGroupName($jsonDataRequest);
        $jsonData['description'] = $this->gValidateDescription($jsonDataRequest);

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->getModel();

        $group->setUserId($userId)
            ->setGroupName($jsonData['group_name'])
            ->setDescription($jsonData['description'])
            ->setSilhouette(1);

        $group = $groupManager->createModel($group);
        $this->createActivity($group->getGroupId(),$userId,null,Activity::GROUP_TYPE,Activity::GROUP_TYPE);

        $groupResult = $group->asArray();
        $finalResult['data'] = $groupResult;

        return new JsonResponse($finalResult, 200);

    }

    function handleGetGroup(Request $request, $groupId)
    {
        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->readModelOneBy(array(
            'group_id' => $groupId
        ));

        if(empty($group)) throw new NotFoundException();

        return $group->asArray();
    }

    function handleDeleteGroup(Request $request, $groupId)
    {

        $userId = $request->request->get('user_id');

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group =$groupManager->getModel();
        $group->setDeleted(1);

        $group = $groupManager->updateModel($group, array(
            'group_id' => $groupId,
            'user_id' => $userId
        ));

        if($group <= 0) throw new UnauthorizedClientException();

        return new JsonResponse(null, 200);

    }

    public function handleFollowGroup(Request $request, $groupId)
    {
        $userId = $request->request->get('user_id');

        $this->validateValue($groupId, array(
            new NotBlank(),
            new GroupId()
        ));

        $followManager = $this->modelManagerFactory->getModelManager('group');
        $follow = $followManager->getModel();

        $follow->setUserId($userId)
            ->setParentId($groupId)
            ->setParentType(Activity::GROUP_TYPE);

        $followManager->createModel($follow);

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupManager->incrNumOfFollowers();

        return new JsonResponse(null, 200);
    }

    public function handleShareGroup(Application $app, Request $request, $groupId) {
        $userId = $request->request->get('user_id');

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
        $userId = $request->request->get('user_id');

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

}