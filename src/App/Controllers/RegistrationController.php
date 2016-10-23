<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use App\Paths\Paths;
use App\Security\SaltGenerator;
use App\Security\SecretGenerator;
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
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
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

        $haveFbAccount = false;

        if( $this->service->userExists($email) ) {
            if(!$this->service->userCredentialsExists($email)) $haveFbAccount = true;
            else throw new InvalidRequestException([
                'error_description' => 'User is already registered.', 'error' => 'full_registered'
            ]);
        }

        // generate random salt
        $salt = (new SaltGenerator())->generateSalt();

        // encode password and salt
        $passwordEncoder = new MessageDigestPasswordEncoder();
        $encodedPassword = $passwordEncoder->encodePassword($password, $salt);

        $this->service->startTransaction();
        try {

            if (!$haveFbAccount) {
                $user_id = $this->service->storeUserData($email, $first_name, $last_name);
                $this->redisService->initializeUserStatistics($user_id);
                $this->redisService->initializeUserIdByEmail($email, $user_id);
            }
            $this->service->storeUserCredentialsData($email, $encodedPassword, $salt);

            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw $e;
        }


        return $this->returnOk();

    }

    public function registerDeviceAction (Request $request) {

        $device_id = $request->request->get("device_id");
        $gcm = $request->request->get("gcm");
        $old_gcm = $request->request->get("old_gcm");

        $this->validate($gcm, [
            new NotBlank()
        ]);

        if(!empty($old_gcm) && !empty($device_id)) return $this->updateDeviceGcm($device_id, $gcm, $old_gcm);
        else return $this->registerDevice($gcm);

    }

    public function updateDeviceGcm($device_id, $gcm, $old_gcm) {
        $this->validate($device_id, [
            new NotBlank()
        ]);
        $this->validate($old_gcm, [
            new NotBlank()
        ]);

        $this->service->updateDevice($device_id, $gcm, $old_gcm);

        $this->returnOk();
    }

    public function registerDevice($gcm) {
        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();
        $deviceId = $this->service->registerDevice($gcm, $deviceSecret);

        return new JsonResponse(array(
            'device_id' => $deviceId,
            'device_secret' => $deviceSecret
        ), 200);
    }

    public function authenticateSocialUserAction (Request $request) {

        $fbUser = $this->checkFacebookToken($request);

        $haveAccount = false;
        $email = $fbUser->getEmail();

        if( ($user = $this->service->userExists($email)) ) {
            if( $this->service->userFbAccountExists($user['user_id']) ) return new JsonResponse(array(
                "username" => $email,
            ), 200);
            else $haveAccount = true;
        }

        $fbId = $fbUser->getId();
        if($haveAccount === false) {
            $firstName = $fbUser->getFirstName();
            $lastName = $fbUser->getLastName();

            $this->service->startTransaction();
            try {
                $newUserId = $this->service->storeUserData($email, $firstName, $lastName);
                $this->service->storeFacebookData($newUserId, $fbId);
                $this->service->commitTransaction();
            } catch (\Exception $e) {
                $this->service->rollbackTransaction();
                throw $e;
            }
            $this->redisService->initializeUserStatistics($newUserId);
            $this->redisService->initializeUserIdByEmail($email, $newUserId);
        } else {
            $this->service->storeFacebookData($user['user_id'], $fbId);
        }

        return new JsonResponse(array(
            "username" => $email,
        ), 200);

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
            $response = $fb->get('/me?fields=id,email,first_name,last_name', $fbToken);
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