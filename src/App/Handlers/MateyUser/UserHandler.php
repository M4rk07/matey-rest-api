<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.03
 */

namespace App\Handlers\MateyUser;


use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Exception\NotFoundException;
use App\MateyModels\Activity;
use App\MateyModels\FeedEntry;
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Paths\Paths;
use App\Services\PaginationService;
use App\Validators\PositiveInteger;
use App\Validators\UnsignedInteger;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class UserHandler extends AbstractUserHandler
{

    public function handleGetUser(Application $app, Request $request, $id)
    {
        $userId = self::getTokenUserId($request);
        $this->validateValue($id, [
                new NotBlank(),
                new UserId()
        ]);

        $userManager = $this->modelManagerFactory->getModelManager('user');

        $user = $userManager->readModelOneBy(array(
            'user_id' => $id
        ));

        if(empty($user)) throw new NotFoundException();

        $arrModel = $user->asArray();
        $arrModel['followed'] = $this->isFollowing($userId, $user->getUserId());

        return $arrModel;
    }

    public function handleFollow (Application $app, Request $request, $id) {

        $userId = self::getTokenUserId($request);

        $this->validateValue($id, [
            new NotBlank(),
            new UserId()
        ]);

        if($userId == $id) throw new InvalidRequestException();

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follow = $followManager->getModel();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userFrom = $userManager->getModel();
        $userTo = $userManager->getModel();

        $userFrom->setUserId($userId);
        $userTo->setUserId($id);

        $follow->setUserId($userId)
            ->setParentId($id)
            ->setParentType(Activity::USER_TYPE);

        $method = $request->getMethod();

        $userExists = $userManager->readModelOneBy(array(
            'user_id' => $userTo->getUserId()
        ));

        if(empty($userExists)) throw new InvalidRequestException();

        if($method == "PUT") {
            try {
                $followManager->createModel($follow);
            } catch (UniqueConstraintViolationException $e) {
                throw new InvalidRequestException(array(
                    'error_description' => ResponseMessages::DUPLICATE_FOLLOWER
                ));
            }
            $userManager->incrNumOfFollowers($userTo);
            $userManager->incrNumOfFollowing($userFrom);
            $this->createActivity($userFrom->getUserId(), $userTo->getUserId(), Activity::USER_TYPE, null, null, Activity::FOLLOW_ACT);

            $postManager =$this->modelManagerFactory->getModelManager('post');
            $posts = $postManager->readModelBy(array(
                'user_id' => $userTo->getUserId()
            ), array('post_id' => 'DESC'), DefaultNumbers::TRANSFER_ON_FOLLOW);

            $userManager->pushDeck($userFrom, $posts);
        }
        else if ($method == "DELETE") {
            $result = $followManager->deleteModel($follow);
            if($result !== null) {
                $userManager->incrNumOfFollowers($userTo, -1);
                $userManager->incrNumOfFollowing($userFrom, -1);
            }
        }

        return new JsonResponse(array(), 200);

    }

    public function handleGetConnections(Application $app, Request $request, $id, $type)
    {

        $userId = self::getTokenUserId($request);
        $me = false;

        if($id == "me") {
            $id = $userId;
            $me = true;
        }

        $pagParams = $this->getPaginationData($request, array(
            'def_max_id' => null,
            'def_count' => DefaultNumbers::POSTS_LIMIT
        ));

        $this->validateValue($id, [
            new NotBlank(),
            new UserId()
        ]);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $followManager = $this->modelManagerFactory->getModelManager('follow');

        $connectionsArray = array();
        $criteria['parent_type'] = Activity::USER_TYPE;

        if($type == "followers") {
            $criteria['parent_id'] = $id;
            if(!empty($pagParams['max_id'])) $criteria['user_id:<'] = $pagParams['max_id'];
            $followers = $followManager->readModelBy($criteria, array('user_id' => 'DESC'), $pagParams['count']);

        } else {
            $criteria['user_id'] = $id;
            if(!empty($pagParams['max_id'])) $criteria['parent_id:<'] = $pagParams['max_id'];
            $followers = $followManager->readModelBy($criteria, array('parent_id' => 'DESC'), $pagParams['count']);
        }

        foreach($followers as $follower) {
            $connectionsArray[] = $follower->getParentId();
        }

        $users = array();

        if(!empty($followers)) {
            $users = $userManager->readModelBy(array(
                'user_id' => $connectionsArray
            ), null, $pagParams['count'], null, array(
                'user_id', 'first_name', 'last_name', 'full_name', 'location', 'country'
            ));
        }

        $responseData = array();
        if(is_array($users)) {
            foreach ($users as $user) {
                $responseData[] = $this->addConnectionUserToResponse($user, $userId, $me, $type);
            }
        } else if ($users) $responseData[] = $this->addConnectionUserToResponse($users, $userId, $me, $type);

        // GENERATING PAGINATION DETAILS
        $paginationService = new PaginationService($responseData, $pagParams['count'],
            $type == 'followers' ? '/users/'.$id.'/followers' : '/users/'.$id.'/following', 'user_id');

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function addConnectionUserToResponse (User $user, $userId, $me, $type) {
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userVals = array();
        if($user) {
            $userVals = $user->asArray();
            $userVals ['followed'] = $me && $type == 'following' ? true : $this->isFollowing($userId, $user->getId());
        }
        return $userVals;
    }

    public function isFollowing($userId, $followingId) {

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        $follow = $followManager->readModelOneBy(array(
            'user_id' => $userId,
            'parent_id' => $followingId,
            'parent_type' => Activity::USER_TYPE
        ));

        if(!empty($follow)) return true;
        return false;

    }



}