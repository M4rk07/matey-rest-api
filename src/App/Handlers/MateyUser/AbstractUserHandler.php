<?php
namespace App\Handlers\MateyUser;
use App\Handlers\AbstractHandler;
use App\Handlers\Activity\Activity;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.00
 */
abstract class AbstractUserHandler extends Activity implements UserHandlerInterface
{

}