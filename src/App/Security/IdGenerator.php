<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 22.01
 */

namespace App\Security;


class IdGenerator
{

    protected $server_code = 111;

    protected $post_code = 1;
    protected $response_code = 2;

    public function generatePostId($user_id) {
        return uniqid($this->server_code."_".$user_id."_".$this->post_code."_", true);
    }

    public function generateResponseId($user_id) {
        return uniqid($this->server_code."_".$user_id."_".$this->response_code."_", true);
    }

}