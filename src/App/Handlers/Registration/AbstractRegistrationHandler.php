<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 16.26
 */

namespace Matey\Handlers\Registration;


use App\MateyModels\User;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRegistrationHandler implements RegistrationHandlerInterface
{

    protected $validator;
    protected $encoderFactory;
    protected $modelManagerFactory;
    protected $userProvider;

    public function __construct(
        ValidatorInterface $validator,
        EncoderFactoryInterface $encoderFactory,
        ModelManagerFactoryInterface $modelManagerFactory,
        UserProviderInterface $userProvider
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->userProvider = $userProvider;
    }

    public function getUserCoreData ($username) {

        $errors = $this->validator->validate($username, [
            new NotBlank()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }
        $user = $this->userProvider->loadUserByUsername($username);

        if(!empty((array)$user)) {
            //If this is reached, user is fully registered.
            //There is facebook account and standard account.
            if ($user->isFacebookAccount() && $user->isStandardAccount()) throw new InvalidRequestException([
                'error' => 'full_reg',
                'error_description' => 'Hey Mate, you are already with us!'
            ]);
            else if(!$user->isFacebookAccount() && !$user->isStandardAccount()) throw new ServerErrorException();
        }

        return $user;

    }

    public function storeUserCoreData (User $user) {

        $this->userProvider->createModel($user);

    }

}