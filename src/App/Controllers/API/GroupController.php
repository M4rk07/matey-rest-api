<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 22.11
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Group\GroupHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupController extends AbstractController
{

    protected $groupHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        GroupHandlerFactoryInterface $groupHandlerFactory
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->groupHandlerFactory = $groupHandlerFactory;
    }

    public function createGroupAction (Application $app, Request $request) {
        return $this->groupHandlerFactory
            ->getGroupHandler('standard')
            ->createGroup($app, $request);
    }

    public function getGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandlerFactory
            ->getGroupHandler('standard')
            ->getGroup($app, $request, $groupId);
    }

    public function deleteGroupAction (Application $app, Request $request, $groupId) {
        return $this->groupHandlerFactory
            ->getGroupHandler('standard')
            ->deleteGroup($app, $request, $groupId);
    }

}