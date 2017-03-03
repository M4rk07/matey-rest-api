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

    // Database resource table names
    const T_USER = "matey_user";
    const T_FACEBOOK_INFO = "matey_facebook_info";
    const T_FOLLOWER = "matey_follow";
    const T_POST = "matey_post";
    const T_APPROVE = "matey_approve";
    const T_REPLY = "matey_reply";
    const T_BOOST = "matey_boost";
    const T_GROUP_ADMIN = "matey_group_admin";
    const T_GROUP_FAVORITE = "matey_group_favorite";
    const T_LOCATION = "matey_location";
    const T_REREPLY = "matey_rereply";
    const T_SHARE = "matey_share";
    const T_ACTIVITY = "matey_activity";
    const T_ACTIVITY_TYPE = "matey_activity_type";
    const T_DEVICE = "matey_device";
    const T_LOGIN = "matey_login";
    const T_BOOKMARK = "matey_bookmark";
    const T_GROUP = "matey_group";

    // Database authorization table names
    const T_A_USER = "oauth2_user";
    const T_A_ACCESS_TOKEN = "oauth2_access_token";
    const T_A_REFRESH_TOKEN = "oauth2_refresh_token";
    const T_A_CLIENTS = "oauth2_client";
    const T_A_CODES = "oauth2_code";
    const T_A_AUTHORIZE = "oauth2_authorize";
    const T_A_SCOPES = "oauth2_scope";

    // REDIS KEYS
    const KEY_APP = "APP";
    const KEY_USER = "USER";
    const KEY_GROUP = "GROUP";
    const KEY_POST = "POST";
    const KEY_REPLY = "REPLY";


    public function __construct(Connection $db, Client $redis = null)
    {
        $this->db = $db;
        $this->redis = $redis;
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

    public function createModel(ModelInterface $model, $ignore = false)
    {

        $fields = $model->getMysqlValues();

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
        $result = $this->db->executeUpdate("INSERT ". ($ignore ? "IGNORE" : "") ." INTO ".$this->getTableName()." (".$keys.") VALUES(".$qMarks.")",
            $values);

        $model->setId($this->db->lastInsertId());

        return $result > 0 ? $model : null;

    }

    public function readModelAll($limit = null)
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->getTableName() . ($limit == null ? "" : " LIMIT ".$limit));
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

        $sql.= " FROM ".$this->getTableName();

        // ------------------------------------------------------------ WHERE CLAUSE
        $whereStr = [];
        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);

            if(is_array($value)) {
                $inString = $key . " IN (";
                foreach($value as $valuee) {
                    $inString .= "?,";
                }
                $inString = rtrim($inString,',');
                $inString .= ")";
                $whereStr[] = $inString;
            }
            else $whereStr[] = $key . " LIKE ?";
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

        $i = 1;
        // bind WHERE criteria values
        foreach($criteria as $key => $value) {
            if(is_array($value)) {
                foreach($value as $keyy => $valuee) {
                    $prepared->bindValue($i++, $valuee);
                }
            }
            else $prepared->bindValue($i++, $value);
        }

        $prepared->execute();
        $all = $prepared->fetchAll();

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
        $fields = $model->getMysqlValues();

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
                $whereStr.=$key."=? AND ";
                $values[] = $value;
            }
            $whereStr = rtrim($whereStr);
            $whereStr = preg_replace('/ AND$/', '', $whereStr);
        }

        // FINAL QUERY
        $result = $this->db->executeUpdate("UPDATE ".$this->getTableName()." SET ".$setStr.$whereStr,
            $values);

        return $result > 0 ? $model : null;
    }

    public function deleteModel(ModelInterface $model, $criteria = null)
    {
        $criteria = $model->getMysqlValues();

        $whereStr = "";
        if($criteria != null) {
            $whereStr = " WHERE ";
            foreach($criteria as $key => $value) {
                $whereStr.=$key."=".$value." AND ";
            }
            $whereStr=rtrim($whereStr);
            $whereStr = preg_replace('/AND$/', '', $whereStr);
        }

        $result = $this->db->executeUpdate("DELETE FROM ".$this->getTableName().$whereStr);

        return $result > 0 ? $model : null;
    }

    // making objects form array
    public function makeObjects(array $result)
    {
        $modelObjects = array();

        foreach ($result as $modelValues) {
            $className = $this->getClassName();
            $object = new $className();
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

}