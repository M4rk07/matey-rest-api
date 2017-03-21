<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.26
 */

namespace App\Handlers\Search;


use App\Constants\Defaults\DefaultNumbers;
use App\Paths\Paths;
use App\Services\PaginationService;
use App\Services\PaginationServiceOffset;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use NilPortugues\Sphinx\SphinxClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler extends AbstractSearchHandler
{

    public function handleSearch (Request $request, $type) {

        $query = $request->get('q');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if(!isset($query)) throw new InvalidRequestException();
        if(!isset($limit)) $limit = DefaultNumbers::SEARCH_LIMIT;
        if(!isset($offset)) $offset = 0;
        if($type == 'user') {
            $manager = $this->modelManagerFactory->getModelManager('user');
        } else
            $manager = $this->modelManagerFactory->getModelManager('group');

        $result = $manager->search($query, $limit, $offset);

        if($result) {
            $result = $result['matches'];
        }

        $finalResult = array();
        if(!empty($result)) {
            $ids = array();
            foreach ($result as $key => $id) {
                $ids[] = $key;
            }

            $models = $manager->getSearchResults($ids);

            foreach ($models as $model) {
                $finalResult[] = $model->asArray();
            }
        }

        $paginationService = new PaginationServiceOffset($finalResult, $limit, $offset,
            $type == 'user' ? '/search/users?q='.$query : '/search/groups?q='.$query);


        return new JsonResponse($paginationService->getResponse(), 200);
    }

}