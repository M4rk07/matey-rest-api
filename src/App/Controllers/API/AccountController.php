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
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends AbstractController
{

    protected $accountHandlerFactory;

    public function __construct(
        AccountHandlerFactoryInterface $accountHandlerFactory
    ) {
        $this->accountHandlerFactory = $accountHandlerFactory;
    }

    public function createAccountAction(Request $request) {

        $accountType = $request->request->get('type');
        $accountType = !empty($accountType) ? : "standard";

        return $this->accountHandlerFactory
            ->getAccountHandler($accountType)
            ->createAccount($request);

    }

    public function createNewAccountAction(Request $request) {

        $accountType = $request->request->get('type');
        $accountType = !empty($accountType) ? : "standard";

        return $this->accountHandlerFactory
            ->getAccountHandler($accountType)
            ->mergeAccount($request);

    }

}