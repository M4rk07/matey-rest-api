<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 15.20
 */

namespace App\Controllers;

require_once __DIR__.'/../MateyModels/Post.php';

use App\Models\Post;
use App\Services\FollowerService;
use Predis\Client;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    protected $redis;

    public function addPostAction (Request $request) {

        $user_id = $request->request->get("user_id");
        $text = $request->request->get("text");

        $post_id = $this->service->createPost($user_id, $text);

        $post = new Post();
        $post->setPostId($post_id)
            ->setText($text);
        $srlPost= $post->serialize();

        $activityType = "posted";
        $parentType = "post";
        // create activity in database
        $this->service->createActivity($user_id, $post_id, $parentType, $activityType, $srlPost);
        $this->cachePosts($post_id, $user_id, $text);

        return $this->returnOk();

    }

    public function cachePosts ($post_id, $user_id, $text) {
        // CACHE TO REDIS
        $this->redis = new Client();
        // cache new post
        $this->redis->hmset("post:".$post_id, array("user_id" => $user_id, "text" => $text, "time" => time()));
        $this->redis->set("post:num_of_responses:".$post_id, 0);
        $this->redis->incr("user:num_of_posts:".$user_id);
        // push to news feed
        $this->pushToNewsFeeds($post_id, $user_id);
    }

    public function pushToNewsFeeds($post_id, $ofUser) {

        $followers = $this->redis->zrange("followers:".$ofUser,0,-1);
        $followers[] = $ofUser;

        foreach($followers as $follower) {
            $this->redis->lpush("newsfeed:posts:".$follower, $post_id);
            $this->redis->ltrim("newsfeed:posts:".$follower, 0, 300);
        }

    }

}