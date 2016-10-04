<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 6:50 PM
 */

namespace App\OAuth2Models;


use App\Services\BaseService;
use Silex\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserStandardManager extends BaseService implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".*, "
            . self::T_STANDARD_USER . ".password, "
            . self::T_STANDARD_USER . ".salt
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_STANDARD_USER . " USING(id_user) 
            WHERE " . self::T_USER . ".email = ? LIMIT 1",
            array($username)
        );
        // make objects form result
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".*, "
            . self::T_STANDARD_USER . ".password, "
            . self::T_STANDARD_USER . ".salt
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_STANDARD_USER . " USING(id_user) 
            WHERE matey_user.id_user = ? LIMIT 1",
            array($user->getId()));
        // make objects form result
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function supportsClass($class)
    {
        return get_class($this) === $class
        || is_subclass_of($class, get_class($this));
    }

    // making objects form array
    public function makeObjects(array $all)
    {
        $userObjects = [];

        try {

            foreach ($all as $data) {

                $object = new UserStandard();
                $object->setUserId($data['id_user'])
                    ->setFirstName($data['first_name'])
                    ->setLastName($data['last_name'])
                    ->setUsername($data['email'])
                    ->setPassword($data['password'])
                    ->setSalt($data['salt']);

                array_push($userObjects, $object);

            }

        } catch (\Exception $e) {

            return new JsonResponse(array(
                "message" => strval($e->getMessage())
            ), $e->getCode());

        }

        return $userObjects;
    }

}