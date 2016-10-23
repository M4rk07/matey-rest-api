<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.10.16.
 * Time: 16.33
 */

namespace AuthBucket\OAuth2\GrantType;


use App\Controllers\RegistrationController;
use App\OAuth2Models\User;
use App\OAuth2Models\UserManager;
use App\Paths\Paths;
use App\Services\Redis\RedisService;
use App\Services\RegistrationService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SocialExchangeGrantTypeHandler extends AbstractGrantTypeHandler
{

    public function handle(Request $request)
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId();

        if($clientId != '1') {
            throw new InvalidRequestException([
                'error_description' => 'Client is not authorized to use this grant type.',
            ]);
        }

        $response = $this->authenticateSocialUser($request);
        $response = json_decode($response->getBody());
        $username = $response->username;

        // Check and set scope.
        $scope = $this->checkScope($request, $clientId, $username);

        // Generate access_token, store to backend and set token response.
        $parameters = $this->tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope
            );

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }

    public function authenticateSocialUser (Request $request) {

        /*
        $access_token = $request->request->get('access_token');

        $client = new Client();
        return $client->request('POST', Paths::BASE_API_URL.'/authenticate/social', [
            'form_params'   => array(
                'access_token' => $access_token
            ),
        ]);
        */

        $registrationController = new RegistrationController(new RegistrationService(), new RedisService(), $this->validator);
        return $registrationController->authenticateSocialUserAction($request);

    }

}