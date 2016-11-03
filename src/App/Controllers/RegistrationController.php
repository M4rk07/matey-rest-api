<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use App\Handlers\ImageHandler;
use App\MateyManagers\DeviceManager;
use App\MateyManagers\UserManager;
use App\MateyModels\Device;
use App\MateyModels\User;
use App\Paths\Paths;
use App\Security\SaltGenerator;
use App\Security\SecretGenerator;
use App\Services\CloudStorageService;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use AuthBucket\OAuth2\Validator\Constraints\UsernameValidator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookRequest;
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

    public function makeSignature (Request $request) {

        $image = new CloudStorageService();

        return new JsonResponse(array(
            'signature' => $image->generateSignedURL('profile_pictures/100x100/45.jpg', 'GET', 30)
        ), 200);

    }



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
        $userManager = new UserManager();
        $user = $userManager->loadUserByUsername($email);
        if( !empty($user->getUserId()) ) {
                /*
                * If email is already registered, and facebook id is registered also,
                * it means that user have facebook account, but not standard
                */
            if($user->isFacebookAccount() && !$user->isStandardAccount()) throw new InvalidRequestException([
                'error' => 'merge_offer',
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ]);
                /*
                 * This shouldn't ever come true, but just in case.
                 * In this case user will have to use another email to register
                 */

            else if($user->isStandardAccount() && !$user->isFacebookAccount()) throw new InvalidRequestException([
                'error_description' => 'Hey '.$user->getFirstName().', you are already with us!'
            ]);
                /*
                 * If this is reached, user is fully registered.
                 * There is facebook account and standard account.
                 */
            else if(!$user->isFacebookAccount() && !$user->isStandardAccount()) throw new InvalidRequestException([
                'error_description' => 'Hey Mate, you are already with us!'
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
        $user->setUsername($email)
            ->setFirstName($first_name)
            ->setLastName($last_name)
            ->setFullName($fullName)
            ->setSilhouette(1)
            ->setPassword($encodedPassword)
            ->setSalt($salt);

        $this->service->startTransaction();
        try {
            // creating new user
            $user = $userManager->createModel($user);
            // storing credentials
            $userManager->createUserCredentials($user);
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

        $device = new Device();
        $device->setGcm($gcm);
        $device->setDeviceId($device_id);
        $deviceManager = new DeviceManager();
        if(!$deviceManager->updateDevice($device, $old_gcm)) throw new InvalidRequestException();

        /*
         * Device is successfully UPDATED!
         */
        return $this->returnOk();
    }

    public function registerDevice($gcm) {
        // generating secret
        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();
        $device = new Device();
        $device->setGcm($gcm);
        $device->setDeviceSecret($deviceSecret);
        $deviceManager = new DeviceManager();
        $deviceManager->createDevice($device);

        /*
         * Return new device data, ID and SECRET, and device is REGISTERED!
         */
        return new JsonResponse(array(
            'device_id' => $device->getDeviceId(),
            'device_secret' => $device->getDeviceSecret()
        ), 200);
    }

    public function authenticateSocialUserAction (Request $request) {
        /*
         * Check facebook token validity.
         */
        $fbToken = $request->request->get("access_token");

        $this->validate($fbToken, [
            new NotBlank()
        ]);

        $fbUser = $this->checkFacebookToken($fbToken);
        if(empty($fbUser)) throw new InvalidRequestException([
            'error' => 'invalid_fb_token'
        ]);
        /*
         * Check if email exists in the system
         */
        $userManager = new UserManager();
        $user = $userManager->loadUserByUsername($fbUser->getEmail());
        if( !empty($user->getUserId()) ) {
                /*
                * If exists, then checking if there is facebook id, in other words
                * user have facebook account
                */
            if( $user->isFacebookAccount() ) {
                $this->redisService->pushFbAccessToken($user->getUserId(), $fbToken);
                return new JsonResponse(array(
                    "username" => $fbUser->getEmail(),
                ), 200);
            }
                /*
                 * If facebook id doesn't exists, but username does, than user have standard account.
                 * In this case asking for merge.
                 */
            else if(!$user->isFacebookAccount() && $user->isStandardAccount()) throw new InvalidRequestException([
                'error' => 'merge_offer',
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ]);
                /*
                 * If there is not facebook id nor username, there is some server error.
                 */
            else throw new ServerErrorException();
        }

        /*
         * At this point, registration takes place.
         */

        $fullName = $fbUser->getFirstName()." ".$fbUser->getLastName();
        $profilePicture = $fbUser->getPicture();
        $isSilhouette = 0;
        if($profilePicture->isSilhouette()) $isSilhouette = 1;

        $user->setUsername($fbUser->getEmail())
            ->setFirstName($fbUser->getFirstName())
            ->setLastName($fbUser->getLastName())
            ->setFullName($fullName)
            ->setSilhouette($isSilhouette)
            ->setFbId($fbUser->getId())
            ->setFbToken($fbToken);

        /*
         * Starting transaction.
         */
        $this->service->startTransaction();
        try {
            /*
             * Storing user and facebook data in database.
             */
            // creating new user
            $user = $userManager->createModel($user);
            // storing credentials
            $userManager->createFacebookInfo($user);

            /*
             * Store facebook image to cloud storage
             */
            if($isSilhouette == 0) {
                $imgHandler = new ImageHandler();
                $imgHandler->handleFacebookProfilePicture($user);
            }

            $this->service->commitTransaction();
        } catch (\Exception $e) {
            $this->service->rollbackTransaction();
            throw $e;
        }

        /*
         * Registration is over SUCCESSFULLY!
         */
        return new JsonResponse(array(
            "username" => $user->getUsername(),
        ), 200);

    }

    public function checkFacebookToken ($fbToken) {

        $fbCredentials = file_get_contents(getenv("FACEBOOK_APPLICATION_CREDENTIALS"));
        $fbCredentials = json_decode($fbCredentials);
        $app_id = $fbCredentials->app_id;
        $app_secret = $fbCredentials->app_secret;

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
            $response = $fb->get('/me?fields=id,email,first_name,last_name,friends,picture', $fbToken);
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