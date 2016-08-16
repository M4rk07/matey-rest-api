<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 1:14 AM
 */

namespace Matey\OAuth2\Models;

use AuthBucket\OAuth2\Exception\ServerErrorException;
use Matey\OAuth2\Models\AccessToken;
use Matey\OAuth2\Models\AbstractManager;
use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;
use AuthBucket\OAuth2\Model\ModelInterface;

class AccessTokenManager extends AbstractManager implements AccessTokenManagerInterface
{
    public function __construct ($db) {
        parent::__construct($db);
        $this->tableName = "oauth2_access_tokens";
    }

    public function createModel(ModelInterface $model)
    {
        if(!$model instanceof AccessToken) {
            throw new ServerErrorException([
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ]);
        }

        $this->db->insert($this->tableName, array(
            'access_token' => $model->getAccessToken(),
            'token_type' => $model->getTokenType(),
            'client_id' => $model->getClientId(),
            'username' => $model->getUsername(),
            'expires' => $model->getExpires(),
            'scope' => $model->getScope()
        ));

        return $model;

    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement readModelBy() method.
    }

    public function readModelOneBy(array $criteria, array $orderBy = null)
    {
        // TODO: Implement readModelOneBy() method.
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