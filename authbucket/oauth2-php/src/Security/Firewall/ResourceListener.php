<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Firewall;

use App\OAuth2Models\AccessTokenManager;
use App\MateyModels\UserManager;
use App\Services\BackupService;
use App\Services\Redis\RedisService;
use AuthBucket\OAuth2\Exception\ExceptionInterface;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessTokenToken;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Validator\Constraints\AccessToken;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ResourceListener implements OAuth2 resource endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceListener implements ListenerInterface
{
    protected $providerKey;
    protected $tokenStorage;
    protected $authenticationManager;
    protected $validator;
    protected $logger;
    protected $tokenTypeHandlerFactory;
    protected $modelManagerFactory;

    public function __construct(
        $providerKey,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        ModelManagerFactoryInterface $modelManagerFactory
    ) {
        $this->providerKey = $providerKey;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Fetch access_token by token type handler.
        $accessToken = null;
        foreach ($this->tokenTypeHandlerFactory->getTokenTypeHandlers() as $key => $value) {
            try {
                $tokenTypeHandler = $this->tokenTypeHandlerFactory->getTokenTypeHandler($key);
                $accessToken = $tokenTypeHandler->getAccessToken($request);
                break;
            } catch (ExceptionInterface $e) {
                continue;
            }
        }
        if ($accessToken === null) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // access_token must in valid format.
        $errors = $this->validator->validate($accessToken, [
            new NotBlank(),
            new AccessToken(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Resource endpoint access token found for access_token "%s"', $accessToken));
        }

        if (null !== $token = $this->tokenStorage->getToken()) {
            if ($token instanceof AccessTokenToken
                && $token->isAuthenticated()
                && $token->getAccessToken() === $accessToken
            ) {
                return;
            }
        }

        $token = new AccessTokenToken(
            $this->providerKey,
            $accessToken
        );
        $tokenAuthenticated = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($tokenAuthenticated);

        $user = $this->getTokenOwnerId($tokenAuthenticated);
        $request->query->set("token-user-id", $user->getUserId());
    }

    public function getTokenOwnerId ($tokenAuthenticated) {
        /*
         * Checking username against token username
         */
        $tokenUsername = $tokenAuthenticated->getUsername();
        /*
         * Fetching user id from redis storage
         */
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->readModelOneBy(array(
            'email' => $tokenUsername
        ), null, array('user_id'));

        return $user;

    }

}
