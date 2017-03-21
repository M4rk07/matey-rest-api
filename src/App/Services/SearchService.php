<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.3.17.
 * Time: 16.54
 */

namespace App\Services;


use App\Paths\Paths;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;
use NilPortugues\Sphinx\SphinxClient;

class SearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = new SphinxClient();
        $this->client->setServer(Paths::BASE_IP, Paths::SPHINX_PORT);
    }

    public function getAutocomplete ($query) {
        $this->client->setMatchMode(SPH_MATCH_EXTENDED);
        $this->client->setRankingMode(SPH_RANK_PROXIMITY_BM25);
        $this->client->setLimits(0, 10);
        $this->client->setGroupBy ( "auto_text", SPH_GROUPBY_ATTR, "@count desc");
        $result = $this->client->query($query, 'autocomplete_rt');

        $finalResult['data'] = array();
        if(isset($result['matches'])) {
            foreach ($result['matches'] as $res) {
                $finalResult['data'][] = $res['attrs']['auto_text'];
            }
        }

        return $finalResult;
    }

}