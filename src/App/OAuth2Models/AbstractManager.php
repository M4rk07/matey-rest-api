<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 1:14 AM
 */

namespace App\OAuth2Models;

use App\Services\BaseService;
use AuthBucket\OAuth2\Model\ModelInterface;

abstract class AbstractManager extends BaseService
{
    protected $tableName;
    protected $className;


    public function getClassName()
    {
        return $this->className;
    }

    public function createModel(ModelInterface $model)
    {

        $this->db->insert($this->tableName, $model->getValuesAsArray($model));

        return $model;

    }

    public function readModelAll()
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->tableName);
        return $this->makeObjects($all);
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {

        $sql = "SELECT * FROM " . $this->tableName;

        $whereStr = [];
        foreach($criteria as $key => $value) {
            $whereStr[] = $key . " LIKE :" . $key;
        }
        if ($whereStr) {
            $whereStr = implode(" AND ", $whereStr);
        } else {
            $whereStr = '';
        }
        // add WHERE clausule
        $sql .= " WHERE " . $whereStr;

        $orderByStr = [];
        if ($orderBy !== null && is_array($orderBy)) {
            foreach($orderBy as $key => $value) {
                $orderByStr[] = $key . " " . $value;
            }
        }
        if ($orderByStr) {
            $orderByStr = implode(", ", $orderByStr);
        } else {
            $orderByStr = '';
        }
        // add ORDER BY clausule
        $sql .= " ORDER BY " . $orderByStr;

        // add LIMIT clausule
        if($limit !== null) $sql .= " LIMIT " . $limit;
        // add OFFSET clausule
        if($offset !== null) $sql .= " OFFSET " . $offset;

        $prepared = $this->db->prepare($sql);

        // bind WHERE criteria values
        foreach($criteria as $key => $value) {
            $prepared->bindValue(':'.$key, $value);
        }

        $prepared->execute();
        $all = $prepared->fetchAll(PDO::FETCH_ASSOC);
        $models = $this->makeObjects($all);

        return $models ?: null;
    }

    public function readModelOneBy(array $criteria, array $orderBy = null)
    {
        $models = $this->readModelBy($criteria, $orderBy, 1, 0);

        return is_array($models) ? reset($models) : $models;
    }

    public function updateModel(ModelInterface $model)
    {

        $this->db->update($this->tableName, $model->getValuesAsArray($model), array('id' => $model->getId()));

        return $model;
    }

    public function deleteModel(ModelInterface $model)
    {

        $this->db->delete($this->tableName, array('id' => $model->getId()));

        return $model;
    }

    public function makeObjects(array $all)
    {
        $str = "Authorize";
        $modelObjects = [];
        foreach($all as $model) {
            $object = new $this->className;
            $object->setValuesFromArray($model);
            array_push($modelObjects, $object);
        }

        return $modelObjects;
    }

}