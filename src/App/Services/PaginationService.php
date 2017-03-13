<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 17.47
 */

namespace App\Services;


use App\Paths\Paths;
use Silex\Application;

class PaginationService
{
    protected $responseData;
    protected $nextMaxId;
    protected $count;
    protected $route;

    public function __construct($responseData, $nextMaxId, $count, $route)
    {
        $this->responseData = $responseData;
        $this->nextMaxId = $nextMaxId;
        $this->count = $count;
        $this->route = $route;
    }

    public function getResponse () {
        $response['data'] = $this->responseData;
        $response['size'] = count($this->responseData);
        $response['count'] = $this->count;

        $response['_links']['base'] = Paths::BASE_API_URL;
        if($response['size'] == $response['count'])
            $response['_links']['next'] =
                Paths::API_ENDPOINT.'/'.Paths::API_VERSION.$this->route.
                '?max_id='.$this->nextMaxId.'&count='.$this->count;
        /*
        if($response['offset'] != 0)
            $response['_links']['prev'] =
                Paths::API_ENDPOINT.'/'.Paths::API_VERSION.$this->route.
                '?limit='.$this->limit.'&offset='.( ((int)$this->offset-(int)$this->limit) < 0 ? 0 : ((int)$this->offset-(int)$this->limit) );
        */

        return $response;
    }
}