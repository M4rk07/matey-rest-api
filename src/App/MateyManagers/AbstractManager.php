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
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractManager extends BaseService implements ModelManagerInterface
{

    // particular class properties
    protected $tableName;
    protected $className;

    public function __construct($tableName, $className)
    {
        parent::__construct();
        $this->tableName = $tableName;
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    public function createModel(ModelInterface $model)
    {

        $fields = $model->getValuesAsArray();

        $keys = "";
        $values = [];
        $qMarks = "";
        foreach ($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $keys.=$key.",";
            $values[]=$value;
            $qMarks.="?,";
        }
        $keys = rtrim($keys, ",");
        $qMarks = rtrim($qMarks, ",");

        // FINAL QUERY
        $this->db->executeUpdate("INSERT INTO ".$this->tableName." (".$keys.") VALUES(".$qMarks.")",
            $values);

        $model->setId($this->db->lastInsertId());

        return $model;

    }

    public function readModelAll()
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->tableName);
        return $this->makeObjects($all);
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $fields = null)
    {

        $sql = "SELECT ";

        // ------------------------------------------------------------ FIELDS
        $fieldsStr = "";
        if(!empty($fields)) {
            foreach($fields as $field) {
                $fieldsStr .= $field.",";
            }
            $fieldsStr = rtrim($fieldsStr, ',');
        }

        empty($fieldsStr) ? $sql.="*" : $sql.=$fieldsStr;

        $sql.= " FROM ".$this->tableName;

        // ------------------------------------------------------------ WHERE CLAUSE
        $whereStr = [];
        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);
            $whereStr[] = $key . " LIKE :" . $key;
        }
        if ($whereStr) {
            $whereStr = implode(" AND ", $whereStr);
        } else {
            $whereStr = '';
        }

        $sql .= " WHERE " . $whereStr;

        // ------------------------------------------------------------ ORDER BY CLAUSE
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

        if(!empty($orderByStr)) $sql .= " ORDER BY " . $orderByStr;

        // ------------------------------------------------------------ LIMIT CLAUSE
        if($limit !== null) $sql .= " LIMIT " . $limit;
        // ------------------------------------------------------------ OFFSET CLAUSE
        if($offset !== null) $sql .= " OFFSET " . $offset;

        $prepared = $this->db->prepare($sql);

        // bind WHERE criteria values
        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);
            $prepared->bindValue(':'.$key, $value);
        }

        $prepared->execute();
        $all = $prepared->fetchAll();

        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function readModelOneBy(array $criteria, array $orderBy = null)
    {
        $models = $this->readModelBy($criteria, $orderBy, 1, 0);

        return is_array($models) ? reset($models) : $models;
    }

    public function updateModel(ModelInterface $model, array $criteria = null)
    {
        $fields = $model->getValuesAsArray();

        $setStr = "";
        $values = [];
        foreach($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $setStr.=$key."=?,";
            $values[] = $value;
        }
        $setStr = rtrim($setStr,',');

        $whereStr = "";
        if($criteria != null) {
            $whereStr = " WHERE ";
            foreach($criteria as $key => $value) {
                $whereStr.=$key."=".$value." AND ";
            }
            rtrim($whereStr);
            $whereStr = preg_replace('/AND$/', '', $whereStr);
        }

        // FINAL QUERY
        $this->db->executeUpdate("UPDATE ".$this->tableName." SET ".$setStr.$whereStr,
            $values);

        return $model;
    }

    public function deleteModel(ModelInterface $model, $criteria = null)
    {
        $whereStr = "";
        if($criteria != null) {
            $whereStr = " WHERE ";
            foreach($criteria as $key => $value) {
                $whereStr.=$key."=".$value." AND ";
            }
            $whereStr=rtrim($whereStr);
            $whereStr = preg_replace('/AND$/', '', $whereStr);
        }

        $this->db->executeUpdate("DELETE FROM ".$this->tableName.$whereStr);

        return $model;
    }

    // making objects form array
    public function makeObjects(array $all)
    {
        $modelObjects = [];
        foreach ($all as $modelArray) {
            $object = new $this->className();
            $object->setValuesFromArray($modelArray);
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

}