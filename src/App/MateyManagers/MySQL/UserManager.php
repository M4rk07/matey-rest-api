<?php

namespace App\MateyModels;
use App\Algos\Algo;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Security\SaltGenerator;
use App\Services\BaseService;
use App\Services\CloudStorageService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.14
 */
class UserManager extends AbstractManager
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\MateyModels\\User';
    }

    public function getTableName() {
        return self::T_USER;
    }

    public function loadUserByEmail($email)
    {
        $result = $this->db->fetchAll("SELECT *
        FROM ".self::T_USER."
        WHERE email = ? LIMIT 1",
            array($email));

        $models = $this->makeObjects($result);

        return is_array($models) ? reset($models) : $models;
    }


}