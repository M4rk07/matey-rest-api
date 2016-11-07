<?php
namespace App\MateyModels;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerInterface;
use Doctrine\DBAL\Connection;
use Predis\Client;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 16.19
 */
class ModelManagerFactory implements ModelManagerFactoryInterface
{
    protected $managers;
    protected $managersRedis;

    public function __construct(array $models = [], Connection $dbConnection = null, Client $predisConnection = null)
    {
        $managers = [];
        $managersRedis = [];

        foreach ($models as $type => $model) {
            if($dbConnection != null) {
                $className = $model . 'Manager';
                $manager = new $className($dbConnection);
                if (!$manager instanceof ModelManagerInterface) {
                    throw new ServerErrorException();
                }
                $managers[$type] = $manager;
            }

            if($predisConnection != null) {
                $className = $model . 'ManagerRedis';
                $manager = new $className($predisConnection);
                if (!$manager instanceof ModelManagerRedisInterface) {
                    throw new ServerErrorException();
                }
                $managersRedis[$type] = $manager;
            }
        }

        $this->managers = $managers;
        $this->managersRedis = $managersRedis;
    }

    public function getModelManager($type, $managerType = 'mysql')
    {
        if($managerType == 'mysql') {
            if (!isset($this->managers[$type])) {
                throw new ServerErrorException();
            }

            return $this->managers[$type];
        } else if($managerType == 'redis') {
            if (!isset($this->managersRedis[$type])) {
                throw new ServerErrorException();
            }

            return $this->managersRedis[$type];
        }
    }

}