<?php

namespace App\Handlers\Group;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.37
 */
interface GroupHandlerInterface
{
    function handleCreateGroup(Request $request);
    function handleGetGroup(Request $request, $groupId);
    function handleDeleteGroup(Request $request, $groupId);
    function handleFollowGroup(Request $request, $groupId);
    public function handleGetFollowingGroups (Request $request, $userId);
}