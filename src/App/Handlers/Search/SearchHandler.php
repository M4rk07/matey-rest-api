<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.26
 */

namespace App\Handlers\Search;


use App\Services\PaginationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler extends AbstractSearchHandler
{

    public function handleSearch (Request $request) {

        $query = $request->get('q');
        $criteria = $request->get('criteria');

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $users = $userManager->search($query);

        $finalUsers = array();
        foreach ($users as $user) {
            $finalUsers = $user->asArray();
        }

        $finalResponse['data']['users'] = $finalUsers;

        return new JsonResponse($finalResponse, 200);
    }

}