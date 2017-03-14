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

    public function __construct($responseData, $count, $route, $identifier)
    {
        $this->responseData = $responseData;
        $this->count = $count;
        $this->route = $route;
        $this->nextMaxId = $this->getNexMaxId($responseData, $identifier);
    }

    public function getNexMaxId($responseData, $identifier) {
        if(($resultNum = count($responseData)) > 0) {
            if (!is_array($identifier))
                return $responseData[$resultNum - 1][$identifier];
            else {
                $arr = $responseData[$resultNum - 1];
                foreach ($identifier as $key) {
                    $arr = $arr[$key];
                }
                return $arr;
            }
        }
        return null;
    }

    public function getResponse () {
        $response['data'] = $this->responseData;
        $response['pagination']['size'] = count($this->responseData);
        $response['pagination']['count'] = $this->count;

        $response['pagination']['_links']['base'] = Paths::BASE_API_URL;
        if($response['pagination']['size'] == $response['pagination']['count'])
            $response['pagination']['_links']['next'] =
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