<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.07
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\MateyUser\UserHandlerFactoryInterface;
use App\Handlers\Profile\ProfileHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    protected $userHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        UserHandlerFactoryInterface $userHandlerFactory
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->userHandlerFactory = $userHandlerFactory;
    }

    public function getUserAction(Request $request, $userId) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->getUser($request, $userId);
    }

    public function followAction(Request $request, $id, $action) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->follow($request, $id);
    }

}