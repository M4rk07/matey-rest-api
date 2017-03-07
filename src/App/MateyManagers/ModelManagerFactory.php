<?php
namespace App\MateyModels;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
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

    public function __construct(array $modelsConfig = [], Connection $dbConnection = null, Client $predisConnection = null)
    {
        $managers = [];

        foreach ($modelsConfig as $type => $config) {
            if($dbConnection != null) {
                $className = $config['class_name'] . 'Manager';
                $manager = new $className($dbConnection, $predisConnection, $config);
                if (!$manager instanceof ModelManagerInterface) {
                    throw new ServerErrorException();
                }
                $managers[$type] = $manager;
            }
        }

        $this->managers = $managers;
    }

    public function getModelManager($type, $managerType = 'mysql')
    {
        if (!isset($this->managers[$type])) {
            throw new ServerErrorException();
        }
        return $this->managers[$type];
    }

}