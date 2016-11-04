<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.10.16.
 * Time: 01.08
 */

namespace App\Controllers;


use App\Algos\Algo;
use App\MateyManagers\DeviceManager;
use App\MateyManagers\LoginManager;
use App\MateyManagers\UserManager;
use App\MateyModels\Device;
use App\MateyModels\Login;
use App\MateyModels\User;
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

        $login = new Login();

        $deviceManager = new DeviceManager();
        $loginManager = new LoginManager();

        $login->setUserId($user_id);
        $login->setDeviceId($deviceId);

        $device = $deviceManager->getDeviceWithGcmById($deviceId);
        $login->setGcm($device->getGcm());
        // store user login information
        // on which device he is logging in
        $this->service->startTransaction();
        try {
            $loginManager->createLogin($login);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        $userManager = new UserManager();
        $user = $userManager->loadUserDataById($user_id);

        $cloudStorage = new CloudStorageService();
        $user->setProfilePicture( $cloudStorage->generateProfilePictureLink($user->getUserId(), $profilePictureSize) );
        $suggestedFollowings = array();

        if($user->isFirstLogin() && $user->isFacebookAccount()) {
            $fbToken = $this->redisService->getFbToken($user->getUserId());
            $fbUserFriends = $this->fetchFacebookFriends($fbToken);

            $friendsIds = [];

            foreach($fbUserFriends as $friend) {
                $friendsIds[] = $friend['id'];
            }

            $suggestedFollowings = $userManager->getSuggestedFollowingsByFacebook($user, $friendsIds, $profilePictureSize);
            //$this->service->setUserFirstTimeLogged($user_id);
        }

        return JsonResponse::create(array(
            'user_id' => $user->getUserId(),
            'email' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'full_name' => $user->getFullName(),
            'first_login' => $user->isFirstLogin(),
            'profile_picture' => $user->getProfilePicture(),
            'is_silhouette' => $user->isSilhouette(),
            'suggested_friends' => $suggestedFollowings
        ), 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

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

        $login = new Login();

        $deviceManager = new DeviceManager();
        $loginManager = new LoginManager();

        $login->setUserId($user_id);
        $login->setDeviceId($deviceId);

        $device = $deviceManager->getDeviceWithGcmById($deviceId);
        $login->setGcm($device->getGcm());
        // store user login information
        // on which device he is logging in
        $this->service->startTransaction();
        try {
            $loginManager->deleteLogin($login);
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        return $this->returnOk();

    }

}