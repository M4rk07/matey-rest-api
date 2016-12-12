<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 15.36
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\File\FileHandlerFactoryInterface;
use App\Handlers\File\FileHandlerInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileController extends AbstractController
{

    protected $fileHandler;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        FileHandlerFactoryInterface $fileHandler
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->fileHandler = $fileHandler;
    }

    public function uploadProfilePictureAction (Application $app, Request $request) {
        return $this->fileHandler
            ->getFileHandler('profile_picture')
            ->upload($app, $request);
    }

}