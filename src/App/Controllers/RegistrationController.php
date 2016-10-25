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
use AuthBucket\OAuth2\Exception\ServerErrorException;
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

        /*
         * fetch required data for registration from request
         */
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $first_name = $request->request->get('first_name');
        $last_name = $request->request->get('last_name');

        /*
         * Validating every parameter
         */
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

        /*
         * Checking if user already exists in the system
         * If does, then respond that he is already facebook user and offer to merge accounts
         */
        if( $user = $this->service->userExists($email) ) {
                /*
                * If email is already registered, and facebook id is registered also,
                * it means that user have facebook account, but not standard
                */
            if(empty($user['username']) && !empty($user['fb_id'])) throw new InvalidRequestException([
                'error' => 'fb_registered'
            ]);
                /*
                 * This shouldn't ever come true, but just in case.
                 * In this case user will have to use another email to register
                 */

            else if(!empty($user['username']) && empty($user['fb_id'])) throw new InvalidRequestException([
                'error' => 'stnd_registered'
            ]);
                /*
                 * If this is reached, user is fully registered.
                 * There is facebook account and standard account.
                 */
            else if(!empty($user['username']) && !empty($user('fb_id'))) throw new InvalidRequestException([
                'error' => 'full_registered'
            ]);
            else throw new ServerErrorException();
        }

        // generating random salt
        $salt = (new SaltGenerator())->generateSalt();
        // encoding password and salt
        $passwordEncoder = new MessageDigestPasswordEncoder();
        $encodedPassword = $passwordEncoder->encodePassword($password, $salt);

        $fullName = $first_name . " " . $last_name;
        /*
         * Starting registration through transaction
         * If any of steps goes wrong, rolls back everything
         */
        $this->service->startTransaction();
        try {
            // storing standard user data
            $user_id = $this->service->storeUserData($email, $first_name, $last_name, $fullName);

            // redis statistics and user id by email finding
            $this->redisService->initializeUserStatistics($user_id);
            $this->redisService->initializeUserIdByEmail($email, $user_id);
            // storing credentials
            $this->service->storeUserCredentialsData($user_id, $email, $encodedPassword, $salt);

            // finally - COMMIT
            $this->service->commitTransaction();
        } catch (\Exception $e) {
            /*
             * Something went wrong with storage.
             * In this case rollback mysql query and throw server error.
             */
            $this->service->rollbackTransaction();
            throw new ServerErrorException();
        }

        /*
         * Everything went ok, user is REGISTERED!
         */
        return $this->returnOk();
    }

    public function registerDeviceAction (Request $request) {

        /*
         * fetch required data for device registration
         */
        $device_id = $request->request->get("device_id");
        $gcm = $request->request->get("gcm");
        $old_gcm = $request->request->get("old_gcm");

        /*
         * Validating gcm
         */
        $this->validate($gcm, [
            new NotBlank()
        ]);

            /*
            * If all parameters are here, then it is updating device operation
            */
        if(!empty($old_gcm) && !empty($device_id) && !empty($gcm)) return $this->updateDeviceGcm($device_id, $gcm, $old_gcm);
            /*
             * Else if only gcm token is here, then register new device
             */
        else if(!empty($gcm)) return $this->registerDevice($gcm);
            /*
             * In every other case, throw invalid request
             */
        else throw new InvalidRequestException();

    }

    public function updateDeviceGcm($device_id, $gcm, $old_gcm) {
        /*
         * Validating parameters
         */
        $this->validateNumericUnsigned($device_id);
        $this->validate($old_gcm, [
            new NotBlank()
        ]);

        /*
         * Update device with new gcm token
         */
        $affectedRows = $this->service->updateDevice($device_id, $gcm, $old_gcm);

        if($affectedRows<=0) throw new InvalidRequestException();
        /*
         * Device is successfully UPDATED!
         */
        return $this->returnOk();
    }

    public function registerDevice($gcm) {
        // generating secret
        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();
        /*
         * Register new device and return device id
         */
        $deviceId = $this->service->registerDevice($gcm, $deviceSecret);

        /*
         * Return new device data, ID and SECRET, and device is REGISTERED!
         */
        return new JsonResponse(array(
            'device_id' => $deviceId,
            'device_secret' => $deviceSecret
        ), 200);
    }

    public function authenticateSocialUserAction (Request $request) {
        /*
         * Check facebook token validity.
         */
        $fbUser = $this->checkFacebookToken($request);
        $email = $fbUser->getEmail();

        /*
         * Check if email exists in the system
         */
        if( ($user = $this->service->userExists($email)) ) {
                /*
                * If exists, then checking if there is facebook id, in other words
                * user have facebook account
                */
            if( !empty($user['fb_id']) ) return new JsonResponse(array(
                "username" => $email,
            ), 200);
                /*
                 * If facebook id doesn't exists, but username does, than user have standard account.
                 * In this case asking for merge.
                 */
            else if(empty($user['fb_id']) && !empty($user['username'])) throw new InvalidRequestException([
                'error' => 'stnd_registered'
            ]);
                /*
                 * If there is not facebook id nor username, there is some server error.
                 */
            else throw new ServerErrorException();
        }

        /*
         * At this point, registration takes place.
         */
        $fbId = $fbUser->getId();
        $firstName = $fbUser->getFirstName();
        $lastName = $fbUser->getLastName();
        $profilePicture = $fbUser->getPicture();
        $fullName = $fbUser->getName();

        /*
         * Starting transaction.
         */
        $this->service->startTransaction();
        try {
            /*
             * Storing user and facebook data in database.
             */
            $newUserId = $this->service->storeUserData($email, $firstName, $lastName, $fullName, $profilePicture);
            $this->service->storeFacebookData($newUserId, $fbId);
            $this->redisService->initializeUserStatistics($newUserId);
            $this->redisService->initializeUserIdByEmail($email, $newUserId);

            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw $e;
        }

        /*
         * Registration is over SUCCESSFULLY!
         */
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