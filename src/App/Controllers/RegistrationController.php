<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;

class RegistrationController extends AbstractController
{

    public function registerStandardUserAction (Request $request) {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $first_name = $request->request->get('first_name');
        $last_name = $request->request->get('last_name');
        $birth_year = $request->request->get('birth_year');

        if( $this->service->userExists($email) ) {
            throw new InvalidRequestException([
                'error_description' => 'User is already registered.',
            ]);
        }

        // register user on authorization server
        $client = new Client();
        $client->request('POST', 'http://localhost/matey-oauth2/web/index.php/api/oauth2/register/user', [
            'form_params'   => array(
                'username' => $email,
                'password' => $password
            ),
        ]);

        $this->service->storeUserData($email, $first_name, $last_name, $birth_year);

        return new JsonResponse(array('success' => true), 200);

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
            $birthYear = $fbUser->getBirthday();

            $newUserId = $this->service->storeUserData($email, $firstName, $lastName, $birthYear);
            $this->service->storeFacebookData($newUserId, $fbId);
        } else {
            $this->service->storeFacebookData($user['id_user'], $fbId);
        }

        $parametres = array(
          "username" => $email,
        );

        return new JsonResponse($parametres, 200);

    }

    public function checkFacebookToken (Request $request) {

        $fbToken = $request->request->get("access_token");

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