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
use App\MateyModels\Follow;
use App\MateyModels\User;
use App\Paths\Paths;
use App\Validators\PositiveInteger;
use App\Validators\UnsignedInteger;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

        if($id == "me") $id = $request->request->get('user_id');
        else $this->validateValue($id, [
                new NotBlank(),
                new UserId()
        ]);


        $userManager = $this->modelManagerFactory->getModelManager('user');

        $user = $userManager->readModelOneBy(array(
            'user_id' => $id
        ));

        if(empty($user)) throw new NotFoundException();

        return new JsonResponse($user->getValuesAsArray(), 200);
    }

    public function follow (Request $request, $id) {

        $userId = $request->request->get('user_id');

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

        $userFrom->setId($userId);
        $userTo->setId($id);

        $follow->setUserId($userId)
            ->setParentId($id)
            ->setParentType(Activity::USER_TYPE);

        $method = $request->getMethod();

        if($method == "POST") {
            try {
                $followManager->createModel($follow);
            } catch (UniqueConstraintViolationException $e) {
                throw new InvalidRequestException(array(
                    'error_description' => ResponseMessages::DUPLICATE_FOLLOWER
                ));
            }
            $userManager->incrNumOfFollowers($userTo);
            $userManager->incrNumOfFollowing($userFrom);
            $this->pushToFeeds($userFrom, $userTo);
        }
        else if ($method == "DELETE") {
            $followManager->deleteModel($follow);
            $userManager->incrNumOfFollowers($userTo, -1);
            $userManager->incrNumOfFollowing($userFrom, -1);
        }

        return new JsonResponse(array(), 200);

    }

    public function getConnections(Application $app, Request $request, $id, $type)
    {

        $userId = $request->request->get('user_id');
        $limit = $request->get('limit');
        $offset = $request->get('offset');
        $me = false;

        if($id == "me") {
            $id = $userId;
            $me = true;
        }

        if(empty($limit)) $limit = DefaultNumbers::PAG_LIMIT_FOLLOWERS;

        $this->validateValue($limit, [
            new NotBlank(),
            new UnsignedInteger()
        ]);

        $this->validateValue($offset, [
            new NotBlank(),
            new PositiveInteger()
        ]);

        $this->validateValue($id, [
            new NotBlank(),
            new UserId()
        ]);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $followManager = $this->modelManagerFactory->getModelManager('follow');

        $connectionsArray = array();

        if($type == "followers") {
            $followers = $followManager->readModelBy(array(
                'parent_id' => $id,
                'parent_type' => "USER"
            ), null, $limit, $offset);
            foreach($followers as $follower) {
                $connectionsArray[] = $follower->getUserId();
            }

        } else {
            $followers = $followManager->readModelBy(array(
                'user_id' => $id,
                'parent_type' => "USER"
            ), null, $limit, $offset);
            foreach($followers as $follower) {
                $connectionsArray[] = $follower->getParentId();
            }
        }

        $users = array();

        if(!empty($followers)) {
            $users = $userManager->readModelBy(array(
                'user_id' => $connectionsArray
            ), null, $limit, null, array(
                'user_id', 'first_name', 'last_name', 'full_name', 'location', 'country'
            ));
        }

        $response['data'] = array();

        if(is_array($users)) {
            foreach ($users as $user) {
                $response['data'][] = $this->addConnectionUserToResponse($user, $userId, $me, $type);
            }
        } else if ($users) $response['data'][] = $this->addConnectionUserToResponse($users, $userId, $me, $type);

        // GENERATING PAGINATION DETAILS

        $response['size'] = count($response['data']);
        $response['offset'] = (int)$offset;
        $response['limit'] = (int)$limit;

        $response['_links']['base'] = Paths::BASE_API_URL;
        if($response['size'] == $response['limit'])
        $response['_links']['next'] =
            $app['api.endpoint'].'/'.$app['api.version'].'/users/'.$id.'/'.$type.
            '?limit='.$limit.'&offset='.((int)$offset+(int)$limit);
        if($response['offset'] != 0)
        $response['_links']['prev'] =
            $app['api.endpoint'].'/'.$app['api.version'].'/users/'.$id.'/'.$type.
            '?limit='.$limit.'&offset='.( ((int)$offset-(int)$limit) < 0 ? 0 : ((int)$offset-(int)$limit) );

        return new JsonResponse($response, 200);

    }

    public function addConnectionUserToResponse (User $user, $userId, $me, $type) {
        $userVals = array();
        if($user) {
            $userVals = $user->getValuesAsArray();
            $userVals ['following'] = $me && $type == 'following' ? true : $this->isFollowing($userId, $user->getId());
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

    // Method for pushing three last posts to following user
    public function pushToFeeds(User $userFrom, User $userTo) {
        $postManager = $this->modelManagerFactory->getModelManager('post');

        $posts = $postManager->readModelBy(array(
            'user_id' => $userTo->getId()
        ), 'time_c', DefaultNumbers::POSTS_NUM_ON_FOLLOW, 0, 'post_id', 'DESC');

        if(empty($posts)) return;

        $postIds = array();
        foreach ($posts as $post) {
            $postIds[] = $post->getId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userManager->pushFeedForCalculation($userFrom, $postIds);

    }

}