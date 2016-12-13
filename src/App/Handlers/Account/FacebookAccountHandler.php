<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 17.02
 */

namespace App\Handlers\Account;


use App\Exception\AlreadyRegisteredException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;

class FacebookAccountHandler extends AbstractAccountHandler
{

    public function createAccount(Request $request)
    {
        /*
         * Get facebook access token
         */
        $fbToken = $request->request->get("access_token");
        $this->validator->validate($fbToken, [
            new NotBlank()
        ]);

        /*
         * Get facebook api client credentials
         */
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

        /*
         * Check provided facebook token and fetch owner data
         */
        $fbUser = $this->checkFacebookToken($fbToken, $fb, $app_id);
        if(empty($fbUser)) throw new InvalidRequestException([
            'error' => 'invalid_fb_token'
        ]);

        $email = $fbUser->getEmail();
        $user = $this->getAccountByEmail($email);
        if(!empty($user)) {
            /*
             * Fetch facebook data of user
             */
            $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo');
            $facebookInfo = $facebookInfoManager->readModelOneBy(array(
                'user_id' => $user->getId()
            ));

                /*
                 * If already registered with facebook,
                 * return username
                 */
                if($facebookInfo) {
                    $facebookInfo->setFbToken($fbToken);
                    $facebookInfoManager->pushFbAccessToken($facebookInfo);
                    return new JsonResponse(array(
                        "username" => $user->getEmail(),
                    ), 200);
                }

                /*
                 * If there is no facebook account,
                 * at this point it means there is oauth2 credentials provided earlier
                 */
                throw new AlreadyRegisteredException(true, [
                    'email' => $user->getEmail(),
                    'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
                ]);
        }

        $fbUser = $this->fetchFacebookData($fbToken, $fb);
        $profilePicture = $fbUser->getPicture();
        $isSilhouette = false;
        if($profilePicture->isSilhouette()) $isSilhouette = true;

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo');

        $userClass = $userManager->getClassName();
        $facebookInfoClass = $facebookInfoManager->getClassName();

        $user = new $userClass();
        $facebookInfo= new $facebookInfoClass();

        $user->setEmail($fbUser->getEmail())
            ->setFirstName($fbUser->getFirstName())
            ->setLastName($fbUser->getLastName())
            ->setSilhouette($isSilhouette);

        $facebookInfo->setFbToken($fbToken)
            ->setFbId($fbUser->getId());

        /*
         * Storing necessary data about facebook user.
         */
        $userManager->startTransaction();
        try {
            // creating new user
            $user = $this->storeUserData($user);

            $facebookInfo->setId($user->getId());
            $facebookInfoManager->createModel($facebookInfo);
            /*
             * Store facebook image to cloud storage
             */
            /*
            if($isSilhouette == 0) {
                $imgHandler = new ImageHandler();
                $imgHandler->handleFacebookProfilePicture($user);
            }*/
            $userManager->commitTransaction();
        } catch (\Exception $e) {
            $userManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        /*
         * Registration is over SUCCESSFULLY!
         */
        return new JsonResponse(array(
            "username" => $user->getEmail()
        ), 200);
    }

    public function mergeAccount(Request $request)
    {
        $userId = $request->request->get('user_id');
        $user = $this->getAccountById($userId);

        if(empty($user)) return new ResourceNotFoundException();

        $fbToken = $request->request->get("fb_token");

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

        $fbUser = $this->checkFacebookToken($fbToken, $fb, $app_id);

        $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
        $facebookInfoClass = $facebookInfoManager->getClassName();
        $facebookInfo= new $facebookInfoClass();

        $facebookInfo->setId($user->getId())
            ->setFbToken($fbToken)
            ->setFbId($fbUser->getId());

        /*
         * Create facebook info in database.
         */
        $facebookInfoManager->createModel($facebookInfo);

        return new JsonResponse(array(), 200);

    }

    public function checkFacebookToken ($fbToken, Facebook $fb, $app_id) {


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

    public function fetchFacebookData($fbToken, Facebook $fb) {

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