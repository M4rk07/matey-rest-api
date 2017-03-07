<?php

namespace App\Handlers\Feed;
use App\Algos\FeedRank\FeedRank;
use App\Constants\Defaults\DefaultDates;
use App\MateyModels\Activity;
use App\MateyModels\FeedEntry;
use App\MateyModels\Group;
use App\MateyModels\Post;
use App\MateyModels\PostManager;
use App\MateyModels\User;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 5.3.17.
 * Time: 22.12
 */
class FeedHandler extends AbstractFeedHandler
{
    public function getFeed (Application $app, Request $request) {

        $userId = $request->request->get('user_id');
        $limit = $request->query->get('limit');
        $offset = $request->query->get('offset');

    }

    // Method for pushing newly created Post to Feeds
    public function pushUpdateToFeeds(Post $post) {

        $followManager = $this->modelManagerFactory->getModelManager('follow');
        /*
         * If there is no group, than fetch only user followers,
         * otherwise fetch and group followers
         */
        if($post->getGroupId() === Group::DEFAULT_GROUP) {
            $followers = $followManager->readModelBy(array(
                'parent_id' => $post->getUserId(),
                'parent_type' => Activity::USER_TYPE
            ), null, null, null, array('user_id', 'time_c'));
        } else
            $followers = $followManager->getGroupAndUserFollowers($post->getUserId(), $post->getGroupId());

        $this->push($followers, $post);

    }

    public function push ($users, $posts) {

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $feedRank = new FeedRank();

        foreach($posts as $post) {

            $score = $feedRank->score($post->getTimeC(), $post->getNumOfBoosts());
            $feed = array($post->getId() => $score);

            foreach($users as $user) {
                $userManager->pushFeed($user, $feed);
            }

        }

    }

}