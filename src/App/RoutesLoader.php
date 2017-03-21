<?php

namespace App;


use App\Services\BaseService;
use App\Services\Redis\UserRedisService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];
        $apiCachable = $this->app["controllers_factory"];


        // OAuth 2.0 controllers
        $this->app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
            ->bind('api_oauth2_authorize');

        $this->app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
            ->bind('api_oauth2_token');

        $this->app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
            ->bind('api_oauth2_debug');
        // -------------------------------------------------------------------------------------

        // Matey API controllers
        $this->app->post('/devices', 'matey.device_controller:createDeviceAction');
        $api->put('/devices/{deviceId}', 'matey.device_controller:updateDeviceAction');

        $this->app->post('/users/accounts', 'matey.account_controller:createAccountAction');
        $api->post('/users/me/accounts', 'matey.account_controller:createNewAccountAction');

        $api->put('/users/me/devices/{deviceId}/login', 'matey.device_controller:loginOnDeviceAction');
        $api->delete('/users/me/devices/{deviceId}/login', 'matey.device_controller:loginOnDeviceAction');

        // OPERATIONS ON USERS
        $apiCachable->get('/users/{userRequestingId}', 'matey.user_controller:getUserAction');
        $apiCachable->get('/users/{userId}/profile', 'matey.user_controller:getUserAction');
        $api->post('/users/me/users/{id}/follow', 'matey.user_controller:followAction'); // deprecated
        $api->delete('/users/me/users/{id}/follow', 'matey.user_controller:followAction'); // deprecated
        $api->post('/users/me/following/{id}', 'matey.user_controller:followAction');
        $api->delete('/users/me/following/{id}', 'matey.user_controller:followAction');
        $apiCachable->get('/users/{userId}/followers', 'matey.user_controller:getFollowersAction');
        $apiCachable->get('/users/{userId}/following', 'matey.user_controller:getFollowingAction');
        $api->post('/users/me/profiles/pictures', 'matey.user_controller:uploadProfilePictureAction');
        $api->post('/users/me/profiles/covers', 'matey.user_controller:uploadCoverPictureAction');

        // POST CONTROLLER
        $api->post('/posts', 'matey.post_controller:createPostAction');
        $api->delete('/posts/{postId}', 'matey.post_controller:deletePostAction');
        $apiCachable->get('/groups/{groupId}/posts', 'matey.post_controller:getGroupPostsAction');
        $apiCachable->get('/users/{userId}/posts', 'matey.post_controller:getUserPostsAction');
        $apiCachable->get('/posts/{postId}', 'matey.post_controller:getPostAction');
        $api->put('/posts/{postId}/boosts', 'matey.post_controller:boostAction');
        $api->delete('/posts/{postId}/boosts', 'matey.post_controller:boostAction');
        $api->put('/posts/{postId}/shares', 'matey.post_controller:shareAction');
        $api->put('/posts/{postId}/bookmarks', 'matey.post_controller:bookmarkAction');
        $api->delete('/posts/{postId}/bookmarks', 'matey.post_controller:bookmarkAction');
        $api->put('/posts/{postId}/archive', 'matey.post_controller:archiveAction');
        $apiCachable->get('/deck', 'matey.post_controller:getUserDeckAction');
        $apiCachable->get('/groups/{groupId}/deck', 'matey.post_controller:getGroupDeckAction');

        // REPLY CONTROLLER
        $api->post('/posts/{postId}/replies', 'matey.reply_controller:createReplyAction');
        $api->delete('/replies/{replyId}', 'matey.reply_controller:deleteReplyAction');
        $apiCachable->get('/posts/{postId}/replies', 'matey.reply_controller:getRepliesAction');
        $api->put('/replies/{replyId}/approves', 'matey.reply_controller:approveAction');

        // REREPLY CONTROLLER
        $api->post('/replies/{replyId}/rereplies', 'matey.rereply_controller:createRereplyAction');
        $api->delete('/rereplies/{rereplyId}', 'matey.rereply_controller:deleteRereplyAction');
        $apiCachable->get('/replies/{replyId}/rereplies', 'matey.rereply_controller:getRerepliesAction');
        $api->put('/rereplies/{rereplyId}/approves', 'matey.rereply_controller:approveAction');

        // OPERATIONS ON GROUPS
        $api->post('/groups', 'matey.group_controller:createGroupAction');
        $apiCachable->get('/groups/{groupId}', 'matey.group_controller:getGroupAction');
        $api->post('/groups/{groupId}/pictures', 'matey.file_controller:uploadGroupPictureAction');
        $api->delete('/groups/{groupId}', 'matey.group_controller:deleteGroupAction');
        $apiCachable->get('/users/{userId}/groups/following', 'matey.group_controller:getFollowingGroupsAction');

        // SEARCH
        $apiCachable->get('/search/users', 'matey.search_controller:searchUsersAction');
        $apiCachable->get('/search/groups', 'matey.search_controller:searchGroupsAction');
        $apiCachable->get('/autocomplete', 'matey.search_controller:autocompleteAction');

        $this->app->get('/tests/data', 'matey.testingdata_controller:makeData');

        // SOME SETTINGS
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $apiCachable);
        $apiCachable->after(function (Request $request, Response $response) {
            $response->setEtag(md5($response->getContent()));
            $etags = $request->getETags();
            foreach($etags as $etag) {
                if($response->getEtag() == $etag) $response->setContent(null);
            }
        });
    }

}

