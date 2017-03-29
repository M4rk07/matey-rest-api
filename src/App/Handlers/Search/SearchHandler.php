<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.3.17.
 * Time: 17.26
 */

namespace App\Handlers\Search;


use App\Constants\Defaults\DefaultNumbers;
use App\MateyModels\Activity;
use App\Paths\Paths;
use App\Services\PaginationService;
use App\Services\PaginationServiceOffset;
use App\Services\SearchService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use NilPortugues\Sphinx\SphinxClient;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler extends AbstractSearchHandler
{

    public function autocomplete (Application $app, Request $request) {
        $query = $request->get('q');
        return new JsonResponse($app['matey.search_service']->getAutocomplete($query), 200);
    }

    public function handleSearch (Application $app, Request $request, $type) {
        $userId = self::getTokenUserId($request);
        $query = $request->get('q');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if(!isset($query)) throw new InvalidRequestException();
        if(!isset($limit)) $limit = DefaultNumbers::SEARCH_LIMIT;
        if(!isset($offset)) $offset = 0;

        if($type == Activity::USER_TYPE)
            $result = $app['matey.search_service']->search($query, $limit, $offset, IND_MATEY_USER);
        else if ($type == Activity::GROUP_TYPE)
            $result = $app['matey.search_service']->search($query, $limit, $offset, IND_MATEY_GROUP);
        else
            $result = $app['matey.search_service']->search($query, $limit, $offset, IND_MATEY_POST);

        if($result) {
            $result = isset($result['matches']) ? $result['matches'] : null;
        }

        $finalResult = array();
        if(!empty($result)) {
            $ids = array();
            foreach ($result as $key => $id) {
                $ids[] = $key;
            }

            if($type == Activity::USER_TYPE)
                $manager = $this->modelManagerFactory->getModelManager('user');
            else if($type == Activity::GROUP_TYPE)
                $manager = $this->modelManagerFactory->getModelManager('group');
            else
                $manager = $this->modelManagerFactory->getModelManager('post');

            $models = $manager->getSearchResults($ids);
            $userHandler = $app['matey.user_handler.factory']->getUserHandler('user');
            $groupHandler = $app['matey.group_handler'];

            foreach ($models as $model) {
                $arrModel = $model->asArray();
                if($type == Activity::USER_TYPE)
                    $arrModel['followed'] = $userHandler->isFollowing($userId, $model->getUserId());
                else if ($type == Activity::GROUP_TYPE)
                    $arrModel['followed'] = $groupHandler->isFollowing($userId, $model->getGroupId());

                $finalResult[] = $arrModel;
            }
        }

        if($type == 'user')
            $paginationService = new PaginationServiceOffset($finalResult, $limit, $offset,
                '/search/users?q='.$query);
        else if ($type == 'group')
            $paginationService = new PaginationServiceOffset($finalResult, $limit, $offset,
                '/search/groups?q='.$query);
        else
            $paginationService = new PaginationServiceOffset($finalResult, $limit, $offset,
                '/search/posts?q='.$query);

        return $paginationService->getResponse();
    }

}