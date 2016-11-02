<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use App\Algos\Algo;
use App\Paths\Paths;
use App\Services\BaseService;
use App\Services\CloudStorageService;
use App\Services\LoginService;
use AuthBucket\OAuth2\Controller\OAuth2Controller;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\TokenType\BearerTokenTypeHandler;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use Facebook\Facebook;
use GuzzleHttp\Client;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class LoginController extends AbstractController
{

    public function loginAction (Request $request) {
        // fetch data from request
        $user_id = $request->request->get("user_id");
        $deviceId = $request->request->get("device_id");
        $profilePictureSize = $request->get('profilePicture');
        if(!isset($profilePictureSize)) $profilePictureSize = 'small';

        $this->validateNumericUnsigned($deviceId);

        $gcm = $this->service->getDeviceGcm($deviceId);
        // store user login information
        // on which device he is logging in
        $this->service->startTransaction();
        try {
            $userData = $this->service->storeLoginRecord($deviceId, $user_id, $gcm);
            $this->redisService->pushNewLoginGcm($userData['user_id'], $gcm);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        $cloudStorage = new CloudStorageService();
        $userData['profile_picture'] = $cloudStorage->generateProfilePictureLink($user_id, $profilePictureSize);

        if($userData['first_login'] == 0 && !empty($userData['fb_id'])) {
            $userData['suggested_friends'] = $this->suggestFriendsActivity($user_id);
            foreach($userData['suggested_friends'] as $user) {
                $user['profile_picture'] = $cloudStorage->generateProfilePictureLink($user_id, $profilePictureSize);
            }
            //$this->service->setUserFirstTimeLogged($user_id);
        }

        return JsonResponse::create($userData, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

    }

    public function suggestFriendsActivity($user_id) {

        $fbToken = $this->redisService->getFbToken($user_id);
        $fbUserFriends = $this->fetchFacebookFriends($fbToken);

        $friendsIds = [];

        foreach($fbUserFriends as $friend) {
            $friendsIds[] = $friend['id'];
        }

        $onMateyFriends = $this->service->findFriendsByFbId($friendsIds);

        $finalResult = $onMateyFriends;

        return $finalResult;

    }

    public function fetchFacebookFriends ($fbToken) {

        $app_id = '1702025086719722';
        $app_secret = 'd7f4251a562c52bfb45c9daf8354f35d';
        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.2',
            'http_client_handler' => 'stream'
        ]);

        $response = $fb->get('/me/friends', $fbToken);
        $fbUserFriends = $response->getGraphEdge()->asArray();

        return $fbUserFriends;

    }

    // NOT FINISHED
    public function logoutAction (Request $request) {

        $user_id = $request->request->get('user_id');
        $deviceId = $request->request->get('device_id');

        $this->validateNumericUnsigned($deviceId);

        $gcm = $this->service->getDeviceGcm($deviceId);
        $this->service->startTransaction();
        try {
            $this->service->storeLogoutRecord($deviceId, $user_id);
            $this->redisService->deleteLoginGcm($user_id, $gcm);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }

}