<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.03
 */

namespace App\Handlers\MateyUser;


use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class UserHandler extends AbstractUserHandler
{

    public function getUser(Request $request, $id)
    {
        $fields = $request->get('fields');
        if(!empty($fields)) {
            $fields = explode(",", $fields);
        }

        if($id == "me") $id = $request->request->get('user_id');
        else {
            $errors = $this->validator->validate($id, [
                new NotBlank(),
                new UserId()
            ]);
            if (count($errors) > 0) {
                throw new InvalidRequestException([
                    'error_description' => 'The request includes an invalid parameter value.',
                ]);
            }
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');

        $user = $userManager->readModelOneBy(array(
            'user_id' => $id
        ));

        if(!$user) throw new ResourceNotFoundException();

        $user = $userManager->getUserStatistics($user);

        return new JsonResponse($user->getValuesAsArray(), 200);
    }

    public function follow (Request $request, $id) {

        $userId = $request->request->get('user_id');

        $errors = $this->validator->validate($id, [
            new NotBlank(),
            new UserId()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        if($userId == $id) throw new InvalidRequestException();

        $followManager = $this->modelManagerFactory->getModelManager('follow', 'mysql');
        $follow = new Follow();

        $follow->setUserFrom($userId)
            ->setUserTo($id);

        $method = $request->getMethod();

        if($method == "POST") $followManager->createModel($follow);
        else if ($method == "DELETE") $followManager->deleteModel($follow);

        return new JsonResponse(array(), 200);

    }

    public function getConnections(Application $app, Request $request, $id, $type)
    {

        $userId = $request->request->get('user_id');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $errors = $this->validator->validate($limit, [
            new NotBlank(),
            new Type(array(
                'type' => 'numeric'
            ))
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        $errors = $this->validator->validate($offset, [
            new NotBlank(),
            new Type(array(
                'type' => 'numeric'
            ))
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $errors = $this->validator->validate($id, [
            new NotBlank(),
            new UserId()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        if($type == "followers") {
            $users = $userManager->getFollowers($id, $limit, $offset);
        } else $users = $userManager->getFollowing($id, $limit, $offset);

        $response['data'] = array();

        if(is_array($users)) {
            foreach ($users as $user) {
                $response['data'][] = $this->addFollowUserToResponse($user, $userId);
            }
        } else if($users) {
            $response['data'][] = $this->addFollowUserToResponse($users, $userId);
        }

        $response['_links']['next'] =
            $app['api.endpoint'].'/'.$app['api.version'].'/users/'.$id.'/'.$type.
            '?limit='.$limit.'&offset='.((int)$offset+(int)$limit);
        $response['_links']['prev'] =
            $app['api.endpoint'].'/'.$app['api.version'].'/users/'.$id.'/'.$type.
            '?limit='.$limit.'&offset='.((int)$offset-(int)$limit);

        return new JsonResponse($response, 200);

    }

    public function addFollowUserToResponse (User $user, $userId) {
        $userVals = array();
        if($user) {
            $userVals = $user->getValuesAsArray();
            $userVals ['following'] = $this->isFollowing($userId, $user->getId());
        }
        return $userVals;
    }

    public function isFollowing($userId, $followingId) {

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follow = $followManager->readModelOneBy(array(
            'from_user' => $userId,
            'to_user' => $followingId
        ));

        if($follow) return true;
        return false;

    }

}