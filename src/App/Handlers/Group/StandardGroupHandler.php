<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.45
 */

namespace App\Handlers\Group;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardGroupHandler extends AbstractGroupHandler
{
    function createGroup(Application $app, Request $request)
    {
        $user_id = $request->request->get('user_id');

        $groupJson = json_decode($request->getContent());
        $groupName = $groupJson->group_name;
        $description = $groupJson->description;
        $privacy = $groupJson->privacy;

    }

    function deleteGroup(Application $app, Request $request)
    {
        // TODO: Implement deleteGroup() method.
    }

    function followGroup(Application $app, Request $request)
    {
        // TODO: Implement followGroup() method.
    }

}