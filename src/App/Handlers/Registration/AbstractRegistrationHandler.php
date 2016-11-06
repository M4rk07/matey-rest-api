<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.26
 */

namespace App\Handlers\Registration;


use App\MateyModels\ModelManagerFactoryInterface;
use App\MateyModels\UserManager;
use App\MateyModels\UserManagerRedis;
use App\MateyModels\User;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRegistrationHandler implements RegistrationHandlerInterface
{

    protected $validator;
    protected $modelManagerFactory;
    protected $userManager;
    protected $userManagerRedis;
    protected $facebookInfoManager;
    protected $facebookInfoManagerRedis;
    protected $oauth2UserManager;
    protected $oauth2UserManagerRedis;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->userManager = $modelManagerFactory->getModelManager('user', 'mysql');
        $this->userManagerRedis = $modelManagerFactory->getModelManager('user', 'redis');
        $this->facebookInfoManager = $modelManagerFactory->getModelManager('facebookInfo', 'mysql');
        $this->facebookInfoManagerRedis = $modelManagerFactory->getModelManager('facebookInfo', 'redis');
        $this->oauth2UserManager = $modelManagerFactory->getModelManager('oauth2User', 'mysql');
        $this->oauth2UserManagerRedis = $modelManagerFactory->getModelManager('oauth2User', 'redis');
    }

    public function getUserCoreData ($username) {

        $errors = $this->validator->validate($username, [
            new NotBlank(),
            new Email()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $user = $this->userManager->loadUserByEmail($username);

        return $user;

    }

    public function storeUserCoreData (User $user) {

        $errors = $this->validator->validate($user->getFirstName(), [
            new NotBlank(),
            new Name()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        $errors = $this->validator->validate($user->getLastName(), [
            new NotBlank(),
            new Name()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $user->setFullName(
            $user->getFirstName()." ".$user->getLastName()
        );

        $this->userManager->createModel($user);
        $this->userManagerRedis->initializeUserStatistics($user);
        $this->userManagerRedis->initializeUserIdByEmail($user);

    }

}