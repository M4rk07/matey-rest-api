<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 18.52
 */

namespace App\MateyModels;


use App\Services\BaseService;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use AuthBucket\OAuth2\Model\ModelManagerInterface;
use Doctrine\DBAL\Connection;
use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractManager implements ModelManagerInterface
{
    // Database connection holder
    protected $db;
    protected $redis;
    protected $modelConfig;

    public function __construct(Connection $db, Client $redis = null, array $modelConfig)
    {
        $this->db = $db;
        $this->redis = $redis;
        $this->modelConfig = $modelConfig;
    }

    public function getModel()
    {
        $class = $this->getClassName();
        return new $class($this->getAllFields());
    }

    public function getClassName()
    {
        return $this->modelConfig['class_name'];
    }

    public function getTableName()
    {
        return isset($this->modelConfig['table_name']) ? $this->modelConfig['table_name'] : null;
    }

    public function getRedisKey()
    {
        return isset($this->modelConfig['redis_key']) ? $this->modelConfig['redis_key'] : null;
    }

    public function getMysqlFields()
    {
        if(isset($this->modelConfig['mysql_fields'])) {
            return is_array($this->modelConfig['mysql_fields']) ?
                $this->modelConfig['mysql_fields'] : array($this->modelConfig['mysql_fields']);
        }
        else return array();
    }

    public function getRedisFields()
    {
        if(isset($this->modelConfig['redis_fields'])) {
            return is_array($this->modelConfig['redis_fields']) ?
                $this->modelConfig['redis_fields'] : array($this->modelConfig['redis_fields']);
        }
        else return array();
    }

    public function getAdditionalFields()
    {
        if(isset($this->modelConfig['additional_fields'])) {
            return is_array($this->modelConfig['additional_fields']) ?
                $this->modelConfig['additional_fields'] : array($this->modelConfig['additional_fields']);
        }
        else return array();
    }

    public function getAllFields() {
        return array_merge($this->getMysqlFields(), $this->getRedisFields(), $this->getAdditionalFields());
    }

    public function startTransaction() {
        if($this->db->isTransactionActive()) throw new ServerErrorException();
        $this->db->beginTransaction();
    }

    public function commitTransaction() {
        if($this->db->isTransactionActive())
            $this->db->commit();
    }

    public function rollbackTransaction() {
        if($this->db->isTransactionActive())
            $this->db->rollBack();
    }

    public function createModel(ModelInterface $model)
    {

        $fields = $model->asArray($this->getMysqlFields());

        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->insert($this->getTableName());

        $counter = 0;
        foreach($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->setValue($key, '?');
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        $model->setId($this->db->lastInsertId());

        return $result > 0 ? $model : null;

    }

    public function readModelAll()
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->getTableName());
        return $this->makeObjects($all);
    }

    public function readModelBy(array $criteria, array $orderBy = null,
                                $limit = null, $offset = null, array $fields = null)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        if(empty($fields)) $mysqlFields = "*";
        else $mysqlFields = array_intersect($fields, $this->getMysqlFields());

        $queryBuilder->select($mysqlFields)
            ->from($this->getTableName());

        $counter = 0;
        foreach($criteria as $key => $value) {

            if(empty($value) && $value !== 0 && $value != '0' && $value !== null) throw new ServerErrorException();

            $keySign = $this->getSign($key);
            $key = $keySign[0];
            $sign = $keySign[1];

            $key = $this->makeColumnName($key);
            if(is_array($value)) {
                $qString = "";
                foreach($value as $val) {
                    $qString .= "?,";
                    $queryBuilder->setParameter($counter++, $val);
                }
                $qString = trim($qString, ",");
                $queryBuilder->andWhere($key . " IN (". $qString .")");
            }
            else {
                $queryBuilder->andWhere($key . $sign . "?");
                $queryBuilder->setParameter($counter++, $value);
            }
        }

        if(isset($limit))
            $queryBuilder->setMaxResults($limit);
        if(isset($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $queryBuilder->addOrderBy($key, $value);
            }
        }
        if(isset($offset))
            $queryBuilder->setFirstResult($offset);

        $all = $queryBuilder->execute()->fetchAll();

        $models = $this->makeObjects($all);
        return $models;
    }

    public function readModelOneBy(array $criteria, array $orderBy = null, array $fields = null)
    {
        $models = $this->readModelBy($criteria, $orderBy, 1, 0, $fields);

        return is_array($models) && !empty($models) ? reset($models) : null;
    }

    public function updateModel(ModelInterface $model, array $criteria = null)
    {
        $fields = $model->asArray($this->getMysqlFields());

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->update($this->getTableName());

        $counter = 0;
        foreach($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->set($key, "?");
            $queryBuilder->setParameter($counter++, $value);
        }

        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->andWhere($key . "=?");
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        return $result > 0 ? $model : null;
    }

    public function deleteModel(ModelInterface $model)
    {
        $criterias = $model->asArray($this->getMysqlFields());

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->delete($this->getTableName());

        $counter = 0;
        foreach($criterias as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->andWhere($key . "=?");
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        return $result > 0 ? $model : null;
    }

    // making objects form array
    public function makeObjects(array $result)
    {
        $modelObjects = array();

        foreach ($result as $modelValues) {
            $object = $this->getModel();
            $object->setValuesFromArray($modelValues);

            array_push($modelObjects, $object);
        }

        return $modelObjects;
    }

    // Needed because of authbucket defined colum names
    public function makeColumnName ($key) {
        if(strcmp($key, "accessToken") == 0) $key = "access_token";
        else if(strcmp($key, "clientId") == 0) $key = "client_id";
        else if(strcmp($key, "tokenType") == 0) $key = "token_type";
        else if(strcmp($key, "clientSecret") == 0) $key = "client_secret";
        else if(strcmp($key, "redirectUri") == 0) $key = "redirect_uri";
        else if(strcmp($key, "refreshToken") == 0) $key = "refresh_token";

        return $key;
    }

    public function getSign ($key) {
        $keySign = explode(":", $key);

        if(!empty($keySign) && count($keySign) == 2) {
            $sign = $keySign[1];
            $key = $keySign[0];
        } else $sign = "=";

        return array($key, $sign);
    }

}