<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.26
 */

namespace App\Handlers\Search;


use App\Constants\Defaults\DefaultNumbers;
use App\Services\PaginationService;
use App\Services\PaginationServiceOffset;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler extends AbstractSearchHandler
{

    public function handleUserSearch (Request $request) {

        $query = $request->get('q');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if(!isset($query)) throw new InvalidRequestException();
        if(!isset($limit)) $limit = DefaultNumbers::SEARCH_LIMIT;
        if(!isset($offset)) $offset = 0;

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->search($query, $limit, $offset);

        $finalUsers = array();
        foreach ($users as $user) {
            $finalUsers[] = $user->asArray();
        }

        $paginationService = new PaginationServiceOffset($finalUsers, $limit, $offset,
            '/search/users?q='.$query);

        return new JsonResponse($paginationService->getResponse(), 200);
    }

    public function handleGroupSearch (Request $request) {

        $query = $request->get('q');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if(!isset($query)) throw new InvalidRequestException();
        if(!isset($limit)) $limit = DefaultNumbers::SEARCH_LIMIT;
        if(!isset($offset)) $offset = 0;

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groups = $groupManager->search($query, $limit, $offset);

        $finalGroups = array();
        foreach ($groups as $group) {
            $finalGroups[] = $group->asArray();
        }

        $paginationService = new PaginationServiceOffset($finalGroups, $limit, $offset,
            '/search/groups?q='.$query);

        return new JsonResponse($paginationService->getResponse(), 200);
    }

}