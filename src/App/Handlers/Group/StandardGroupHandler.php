<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.45
 */

namespace App\Handlers\Group;


use App\Exception\NotFoundException;
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

class StandardGroupHandler extends AbstractGroupHandler
{
    function createGroup(Application $app, Request $request)
    {

        $userId = $request->request->get('user_id');

        $groupJson = json_decode($request->getContent());
        if ($groupJson === null) throw new InvalidRequestException();

        $groupName = $groupJson->group_name;
        $description = isset($groupJson->description) ? $groupJson->description : null;

        $errors =$this->validator->validate($groupName, array(
            new NotBlank()
        ));
        if(count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => $errors->get(0)->getMessage()
            ));
        }

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupRelationshipManager = $this->modelManagerFactory->getModelManager('group_relationship');
        $groupRelationshipClass = $groupRelationshipManager->getClassName();
        $groupClass =$groupManager->getClassName();
        $groupRelationship = new $groupRelationshipClass();
        $group = new $groupClass();

        $group->setUserId($userId)
            ->setGroupName($groupName)
            ->setSilhouette(1);

        if(!empty($description)) $group->setDescription($description);

        $group = $groupManager->createModel($group);

        $groupRelationship->setGroupId($group->getId())
            ->setUserId($userId)
            ->setRole('OWNER');
        $groupRelationshipManager->createModel($groupRelationship);

        return new JsonResponse($group->getValuesAsArray(), 201, array(
            'Location' => Paths::BASE_API_URL.$app['api.endpoint'].'/'.$app['api.version'].'/groups/'.$group->getId()
        ));

    }

    function getGroup(Application $app, Request $request, $groupId)
    {
        $errors =$this->validator->validate($groupId, array(
            new NotBlank(),
            new GroupId()
        ));
        if(count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => $errors->get(0)->getMessage()
            ));
        }

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->readModelOneBy(array(
            'group_id' => $groupId
        ));

        if(empty($group) || $group->getDeleted() == 1) throw new NotFoundException();

        return new JsonResponse($group->getValuesAsArray(), 200);
    }

    function deleteGroup(Application $app, Request $request, $groupId)
    {

        $userId = $request->request->get('user_id');

        $errors =$this->validator->validate($groupId, array(
            new NotBlank(),
            new GroupId()
        ));
        if(count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => $errors->get(0)->getMessage()
            ));
        }

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupClass =$groupManager->getClassName();
        $group = new $groupClass();
        $group->setDeleted(1);

        $group = $groupManager->updateModel($group, array(
            'group_id' => $groupId,
            'user_id' => $userId
        ));

        if($group==null) throw new UnauthorizedClientException();

        return new JsonResponse(null, 200);

    }

    function followGroup(Application $app, Request $request, $groupId)
    {
        // TODO: Implement followGroup() method.
    }

}