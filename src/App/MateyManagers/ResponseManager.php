<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 13.55
 */

namespace App\MateyManagers;


use App\MateyModels\Response;
use App\Services\BaseService;

class ResponseManager extends BaseService
{

    public function createResponse(Response $response) {

        $result = $this->db->executeUpdate("INSERT INTO ".self::T_RESPONSE." (user_id, post_id, text, date_time) VALUES (?,?,?,?)",
            array($response->getUserId(), $response->getPostId(), $response->getText(), $response->getDateTime()));
        if($result<=0) return false;
        $response->setResponseId($this->db->lastInsertId());

        $this->initializeResponseStatistics($response);

        return $response;
    }

    public function initializeResponseStatistics(Response $response) {
        $this->redis->hmset(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response->getResponseId(), array(
            self::FIELD_NUM_OF_APPROVES => 0
        ));
    }

    public function deleteResponse(Response $response) {
        $result = $this->db->executeUpdate("UPDATE ".self::T_RESPONSE." SET deleted = 1 WHERE response_id = ?",
            array($response->getResponseId()));
        if($result <= 0) return false;
        $this->deleteResponseStatistics($response);
        return $response;
    }

    public function deleteResponseStatistics(Response $response) {
        $this->redis->hdel(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response->getResponseId(), array(
            self::FIELD_NUM_OF_APPROVES
        ));
    }

    public function incrResponseNumOfApproves(Response $response, $incrby) {
        $this->redis->hincrby(self::KEY_RESPONSE.":".self::SUBKEY_STATISTICS.":".$response->getResponseId(), self::FIELD_NUM_OF_APPROVES, $incrby);
    }

}