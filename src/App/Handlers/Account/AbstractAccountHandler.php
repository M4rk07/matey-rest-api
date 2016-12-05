<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 16.41
 */

namespace App\Handlers\Account;


use App\MateyModels\ModelManagerFactoryInterface;
use App\MateyModels\User;
use App\Validators\Name;
use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractAccountHandler implements AccountHandlerInterface
{

    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function getAccountById($userId)
    {
        $errors = $this->validator->validate($userId, [
            new NotBlank(),
            new UserId()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->readModelOneBy(array(
            'user_id' => $userId
        ));

        return $user;
    }

    public function getAccountByEmail($email)
    {
        $email = trim($email, " \t\n\r\0\x0B");

        $errors = $this->validator->validate($email, [
            new NotBlank(),
            new Email()
        ]);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->readModelOneBy(array(
            'email' => $email
        ));

        return $user;
    }

    public function storeUserData(User $user) {

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

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->createModel($user);
        $userManager->initializeUserStatistics($user);
        $userManager->initializeUserIdByEmail($user);

        return $user;
    }

}