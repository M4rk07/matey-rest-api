<?php

namespace App\Handlers\TestingData;
use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 17.34
 */
interface TestingDataHandlerInterface
{

    public function makeOAuth2Accounts ();
    public function makeRelationships ();
    public function fillUsersTable();

}