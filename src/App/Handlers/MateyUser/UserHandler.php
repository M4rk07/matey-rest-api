<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.03
 */

namespace App\Handlers\MateyUser;


use App\MateyModels\Follow;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;

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

    public function getFollowers(Request $request, $id)
    {

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

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        $users = $userManager->getFollowers($id);

        $followers['users'] = array();

        if(is_array($users)) {
            foreach ($users as $user) {
                $userVals = $user->getValuesAsArray();
                $userVals ['is_following'] = $this->isFollowing($id, $user->getId());
                $followers['users'][] = $userVals;
            }
        } else if($users) {
            $userVals = $users->getValuesAsArray();
            $userVals ['following'] = $this->isFollowing($id, $users->getId());
            $followers['users'][] = $userVals;
        }

        return new JsonResponse($followers, 200);

    }

    public function getFollowing(Request $request, $id)
    {

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

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        $users = $userManager->getFollowing($id);

        $followers['users'] = array();

        if(is_array($users)) {
            foreach ($users as $user) {
                $userVals = $user->getValuesAsArray();
                $userVals ['is_following'] = $this->isFollowing($id, $user->getId());
                $followers['users'][] = $userVals;
            }
        } else if($users) {
            $userVals = $users->getValuesAsArray();
            $userVals ['following'] = $this->isFollowing($id, $users->getId());
            $followers['users'][] = $userVals;
        }

        return new JsonResponse($followers, 200);

    }

    public function isFollowing($userId, $followingId) {

        $followManager = $this->modelManagerFactory->getModelManager('follow', 'mysql');
        $follow = $followManager->readModelOneBy(array(
            'from_user' => $userId,
            'to_user' => $followingId
        ));

        if($follow) return true;
        return false;

    }

}