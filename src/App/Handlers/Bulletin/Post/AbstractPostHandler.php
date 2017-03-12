<?php
namespace App\Handlers\Bulletin\Post;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\AbstractHandler;
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

    public function mergePostsAndUsers ($posts, $users) {
        $postManager = $this->modelManagerFactory->getModelManager('post');

        if(!is_array($posts)) $posts = array($posts);
        if(!is_array($users)) $users = array($users);

        $finalResult = array();
        foreach($posts as $post) {
            $arr = $post->asArray(array_diff($postManager->getAllFields(), array('user_id', 'deleted', 'archived')));
            foreach($users as $user) {
                if($user->getUserId() == $post->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }
            if ($post->getAttachsNum() > 0)
                $arr['attachs'] = $post->getAttachsLocation($post->getAttachsNum());
            if ($post->getLocationsNum() > 0)
                $arr['locations'] = $this->getLocations($post->getPostId(), Activity::POST_TYPE, $post->getLocationsNum());

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getPosts ($criteria, $limit = DefaultNumbers::POSTS_LIMIT, $offset = 0) {
        $postManager = $this->modelManagerFactory->getModelManager('post');
        $posts = $postManager->readModelBy($criteria, array('time_c' => 'DESC'), $limit, $offset);

        $userIds = array();
        foreach($posts as $post) {
            $userIds[] = $post->getUserId();
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, $offset, array('user_id', 'first_name', 'last_name'));

        return $this->mergePostsAndUsers($posts, $users);
    }

}