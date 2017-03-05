<?php

namespace App\Handlers\Feed;
use App\MateyModels\FeedEntry;
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

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();
        $user->setId($userId);

        $calculationFeeds = $userManager->getFeedForCalculation($user);

        $postManager = $this->modelManagerFactory->getModelManager('post');

        $calculatedPosts = array();
        foreach($calculationFeeds as $feedEntry) {
            $post = $postManager->getModel();
            $post->setId($feedEntry->getPostId());
            $post = $postManager->getStatisticsData($post);
            $post = $postManager->getPostTimestamp($post);

            $score = $this->feedEntryScore($user, $post);
            $calculatedPosts[] = array('feedEntry' => $feedEntry, 'score' => $score);
        }

        $scoredFeeds = $userManager->getFeedScored($user);

        $postIds = array();
        foreach ($scoredFeeds as $scoredFeed) {
            $postIds[] = $scoredFeed->getPostId();
        }

        $posts = $userManager->readModelBy(array(
            'post_id' => $postIds
        ), null, $limit);

    }

    public function feedEntryScore(User $user, Post $post) {

        return (1/($post->getTimestamp()))*$post->getNumOfBoosts();

    }

}