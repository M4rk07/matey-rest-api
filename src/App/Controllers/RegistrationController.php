<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use App\Validators\FirstName;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use AuthBucket\OAuth2\Validator\Constraints\UsernameValidator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use GuzzleHttp\Client;
use Mockery\Matcher\Not;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;

class RegistrationController extends AbstractController
{

    public function registerStandardUserAction (Request $request) {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $first_name = $request->request->get('first_name');
        $last_name = $request->request->get('last_name');

        $this->validate($email, [
            new NotBlank(),
            new Email()
        ]);
        $this->validate($password, [
            new NotBlank(),
            new Password()
        ]);
        $this->validate($first_name, [
            new NotBlank(),
            new Name()
        ]);
        $this->validate($last_name, [
            new NotBlank(),
            new Name()
        ]);
        $first_name = ucfirst($first_name);
        $last_name = ucfirst($last_name);

        if( $this->service->userExists($email) ) {
            throw new InvalidRequestException([
                'error_description' => 'User is already registered.',
            ]);
        }

        // register user on authorization server
        $client = new Client();
        $client->request('POST', self::BASE_OAuth2_URL.'/api/oauth2/register/user', [
            'form_params'   => array(
                'username' => $email,
                'password' => $password
            ),
        ]);

        $user_id = $this->service->storeUserData($email, $first_name, $last_name);
        $this->redisService->initializeUserStatistics($user_id);

        return $this->returnOk();

    }

    public function registerDeviceAction (Request $request) {

        $gcm = $request->request->get("gcm");

        $this->validate($gcm, [
            new NotBlank()
        ]);

        $deviceId = $this->service->registerDevice($gcm);

        return new JsonResponse(array('device_id' => $deviceId), 200);

    }

    public function authenticateSocialUserAction (Request $request) {

        $fbUser = $this->checkFacebookToken($request);

        $haveAccount = false;
        $email = $fbUser->getEmail();

        if( ($user = $this->service->userExists($email)) ) {
            if(!empty($user['fb_id'])) return new JsonResponse(array(), 200);
            else $haveAccount = true;
        }

        $fbId = $fbUser->getId();
        if($haveAccount == false) {
            $firstName = $fbUser->getFirstName();
            $lastName = $fbUser->getLastName();

            $newUserId = $this->service->storeUserData($email, $firstName, $lastName);
            $this->service->storeFacebookData($newUserId, $fbId);
            $this->redisService->initializeUserStatistics($newUserId);
        } else {
            $this->service->storeFacebookData($user['user_id'], $fbId);
        }

        $parametres = array(
          "username" => $email,
        );

        return new JsonResponse($parametres, 200);

    }

    public function checkFacebookToken (Request $request) {

        $fbToken = $request->request->get("access_token");

        $this->validate($fbToken, [
            new NotBlank()
        ]);

        $app_id = '1702025086719722';
        $app_secret = 'd7f4251a562c52bfb45c9daf8354f35d';
        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.2',
            'http_client_handler' => 'stream'
        ]);
        $oAuth2Client = $fb->getOAuth2Client();
        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($fbToken);
        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($app_id);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId($fbUserId);
        $tokenMetadata->validateExpiration();

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,email', $fbToken);
        } catch(FacebookResponseException $e) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        } catch(FacebookSDKException $e) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        // TAKING THE USER
        $user = $response->getGraphUser();

        return $user;

    }

}