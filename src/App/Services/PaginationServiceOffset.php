<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 19.3.17.
 * Time: 19.39
 */

namespace App\Services;


use App\Paths\Paths;

class PaginationServiceOffset
{

    protected $responseData;
    protected $offset;
    protected $limit;
    protected $route;

    public function __construct($responseData, $limit, $offset, $route)
    {
        $this->responseData = $responseData;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->route = $route;
    }

    public function getResponse () {
        $response['data'] = $this->responseData;
        $response['pagination']['size'] = count($this->responseData);
        $response['pagination']['limit'] = $this->limit;

        $response['pagination']['_links']['base'] = Paths::BASE_API_URL;
        if($response['pagination']['size'] == $response['pagination']['limit'])
            $response['pagination']['_links']['next'] =
                Paths::API_ENDPOINT.'/'.Paths::API_VERSION.$this->route.
                '?limit='.$this->limit.'&offset='.($this->offset+$this->limit);

        return $response;
    }

}