<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 15.27
 */

namespace App\Handlers\Registration;


use App\Handlers\ImageHandler;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use MongoDB\Driver\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class FacebookRegistrationHandler extends AbstractRegistrationHandler
{

    public function handle(Request $request) {

        $fbToken = $request->request->get("access_token");
        $this->validator->validate($fbToken, [
            new NotBlank()
        ]);

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
        if(empty($fbUser)) throw new InvalidRequestException([
            'error' => 'invalid_fb_token'
        ]);

        $username = $fbUser->getEmail();
        $user = $this->getUserCoreData($username);
        if($user) {
            $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
            $facebookInfo = $facebookInfoManager->readModelOneBy(array(
                'user_id' => $user->getUserId()
            ));
            /*
            * If exists, then checking if there is facebook id, in other words
            * user have facebook account
            */
            if($facebookInfo) {
                $facebookInfo->setFbToken($fbToken);
                $facebookInfoManagerRedis = $this->modelManagerFactory->getModelManager('facebookInfo', 'redis');
                $facebookInfoManagerRedis->pushFbAccessToken($facebookInfo);
                return new JsonResponse(array(
                    "username" => $user->getEmail(),
                ), 200);
            }
            /*
             * If facebook id doesn't exists, but username does, than user have standard account.
             * In this case asking for merge.
             */
            throw new InvalidRequestException([
                'error' => 'merge_offer',
                'email' => $user->getEmail(),
                'error_description' => "Hey ".$user->getFirstName().", you are already with us! But we offer you to merge this account with existing account. Say OK and you're in!"
            ], 409);
        }

        $fbUser = $this->fetchFacebookData($fbToken, $fb);
        $profilePicture = $fbUser->getPicture();
        $isSilhouette = 0;
        if($profilePicture->isSilhouette()) $isSilhouette = 1;

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');
        $facebookInfoManager = $this->modelManagerFactory->getModelManager('facebookInfo', 'mysql');
        $facebookInfoManagerRedis = $this->modelManagerFactory->getModelManager('facebookInfo', 'redis');

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
         * Starting transaction.
         */

        $this->dbConnection->beginTransaction();
        try {
            // creating new user
            $user = $this->storeUserCoreData($user);
            $facebookInfo->setUserId($user->getId());
            // storing credentials
            $facebookInfoManager->createModel($facebookInfo);
            $facebookInfoManagerRedis->pushFbAccessToken($facebookInfo);
            /*
             * Store facebook image to cloud storage
             */
            /*
            if($isSilhouette == 0) {
                $imgHandler = new ImageHandler();
                $imgHandler->handleFacebookProfilePicture($user);
            }*/
            $this->dbConnection->commit();
        } catch (\Exception $e) {
            $this->dbConnection->rollBack();
            throw new ServerErrorException();
        }

        /*
         * Registration is over SUCCESSFULLY!
         */
        return new JsonResponse(array(
            "username" => $user->getEmail()
        ), 200);

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