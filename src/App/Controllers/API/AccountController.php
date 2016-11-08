<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.11.16.
 * Time: 17.32
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Account\AccountHandlerFactory;
use App\Handlers\Account\AccountHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends AbstractController
{

    protected $accountHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        AccountHandlerFactoryInterface $accountHandlerFactory
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->accountHandlerFactory = $accountHandlerFactory;
    }

    public function createAccountAction(Request $request, $accountType) {

        return $this->accountHandlerFactory
            ->getAccountHandler($accountType)
            ->createAccount($request);

    }

    public function mergeAccountAction(Request $request, $accountType) {

        return $this->accountHandlerFactory
            ->getAccountHandler($accountType)
            ->mergeAccount($request);

    }

}