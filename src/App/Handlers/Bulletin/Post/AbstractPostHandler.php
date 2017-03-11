<?php
namespace App\Handlers\Bulletin\Post;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\AbstractHandler;
use App\Handlers\Post\AbstractBulletinHandler;
use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.07
 */
abstract class AbstractPostHandler extends AbstractBulletinHandler  implements PostHandlerInterface
{

}