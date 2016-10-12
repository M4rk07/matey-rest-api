<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 19.40
 */

namespace App\Models;


use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;
use AuthBucket\OAuth2\Model\InMemory\AccessToken;
use AuthBucket\OAuth2\Model\ModelInterface;
use Predis\Client;

class AccessTokenManager implements AccessTokenManagerInterface
{
    protected $redis;

    public function __construct()
    {
        $this->redis=new Client();
    }

    public function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    public function createModel(ModelInterface $model)
    {
        // TODO: Implement createModel() method.
    }

    public function readModelAll()
    {
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {

    }

    public function readModelOneBy(array $criteria, array $orderBy = null)
    {
        $tokenData = $this->redis->hgetall("accessToken:".$criteria['accessToken']);
        $accessToken = new AccessToken();
        $accessToken->setUsername($tokenData['username'])
            ->setAccessToken($tokenData['accessToken'])
            ->setClientId($tokenData['clientId'])
            ->setExpires($tokenData['expires'])
            ->setScope($tokenData['scope'])
            ->setTokenType($tokenData['tokenType']);
    }

    public function updateModel(ModelInterface $model)
    {
        // TODO: Implement updateModel() method.
    }

    public function deleteModel(ModelInterface $model)
    {
        // TODO: Implement deleteModel() method.
    }


}