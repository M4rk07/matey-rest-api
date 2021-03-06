<?php
namespace App\Handlers\Bulletin\Post;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\AbstractHandler;
use App\Handlers\File\PostAttachmentHandler;
use App\Handlers\Post\AbstractBulletinHandler;
use App\MateyModels\Activity;
use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.07
 */
abstract class AbstractPostHandler extends AbstractBulletinHandler  implements PostHandlerInterface
{

    protected function mergePostsAndUsers ($posts, $users, $boostedIds) {
        $postManager = $this->modelManagerFactory->getModelManager('post');

        if(!is_array($posts)) $posts = array($posts);
        if(!is_array($users)) $users = array($users);
        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $finalResult = array();
        foreach($posts as $post) {
            $arr = $post->asArray(array_diff($postManager->getAllFields(), array('user_id', 'deleted', 'archived')));
            if($post->getGroupId() !== null) {
                $group = $groupManager->readModelOneBy(array(
                    'group_id' => $post->getGroupId()
                ), null, array('group_id', 'group_name'));
                $arr['group'] = $group->asArray();
                unset($arr['group_id']);
            }
            foreach($users as $user) {
                if($user->getUserId() == $post->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }
            if ($post->getAttachsNum() > 0)
                $arr['attachs'] = PostAttachmentHandler::getAttachUrls($post, PostAttachmentHandler::LOCATION_POSTS);
            if ($post->getLocationsNum() > 0)
                $arr['locations'] = $this->getLocations($post->getPostId(), Activity::POST_TYPE, $post->getLocationsNum());

            if(in_array($post->getPostId(), $boostedIds)) $arr['boosted'] = true;
            else $arr['boosted'] = false;

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    protected function getPosts ($criteria, $userRequestingId, $count = DefaultNumbers::POSTS_LIMIT) {
        $postManager = $this->modelManagerFactory->getModelManager('post');
        $posts = $postManager->readModelBy($criteria, array('post_id' => 'DESC'), $count);

        $userIds = array();
        $postIds = array();
        foreach($posts as $post) {
            $userIds[] = $post->getUserId();
            $postIds[] = $post->getPostId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $count, null, array('user_id', 'first_name', 'last_name', 'picture_url'));

        $boostManager = $this->modelManagerFactory->getModelManager('boost');
        $boosts = $boostManager->readModelBy(array(
            'user_id' => $userRequestingId,
            'post_id' => array_unique($postIds)
        ), null, $count, null);

        $boostedIds = array();
        foreach($boosts as $boost) {
            $boostedIds[] = $boost->getPostId();
        }

        return $this->mergePostsAndUsers($posts, $users, $boostedIds);
    }

}