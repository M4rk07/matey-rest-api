<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 2.10.16.
 * Time: 16.37
 */

namespace App\Handler;

use App\OAuth2Models\Client;
use App\OAuth2Models\ClientManager;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Matey\Validator\Constraints\ClientTypeValidator;

class ClientRegistrationHandler
{

    public function handle(Request $request) {

        $clientType = $request->request->get('client_type');
        $redirectUri = $request->request->get('redirect_uri');
        $clientAppName = $request->request->get('app_name');

        // DODATI REGEX ZA APPNAME
        if( !(new ClientTypeValidator())->validate($clientType) ) {
            return new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        ($client = new Client())->setAppName($clientAppName)
            ->setRedirectUri($redirectUri)
            ->setClientType($clientType)
            ->setClientSecret( md5(openssl_random_pseudo_bytes(128)) );

        $client = ($clientManager = new ClientManager())->createModel($client);

        $parameters = array(
          'client_id' => $client->getId(),
            'client_secret' => $client->getClientSecret()
        );

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);

    }

}