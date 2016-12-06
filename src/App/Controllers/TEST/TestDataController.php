<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.12.16.
 * Time: 17.48
 */

namespace App\Controllers\TEST;


use App\Controllers\AbstractController;
use App\Handlers\TestingData\TestingDataHandler;
use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestDataController extends AbstractController
{
    protected $testDataHandler;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TestingDataHandler $testDataHandler
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->testDataHandler = $testDataHandler;
    }

    public function makeData () {
        $this->testDataHandler
            ->fillUsersTable();

        $this->testDataHandler
            ->makeOAuth2Accounts();

        $this->testDataHandler
            ->makeRelationships();

        return "ok";
    }
}