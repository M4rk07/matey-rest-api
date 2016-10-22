<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.10.16.
 * Time: 23.43
 */

namespace App\OAuth2Models;


use App\Services\BaseService;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserManager extends BaseService implements UserProviderInterface
{

    public function createModel(User $user) {

        $this->db->insert(self::T_A_USER, array(
            'username' => $user->getUsername()
        ));

    }

    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll(
            "SELECT o_user.* 
            FROM ".self::T_A_USER." as o_user 
            WHERE o_user.username = ? LIMIT 1",
            array($username)
        );
        // make objects form result
        $models = $this->makeObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        $all = $this->db->fetchAll(
            "SELECT o_user.* 
            FROM ".self::T_A_USER." as o_user 
            WHERE o_user.user_id = ? LIMIT 1",
            array($user->getUserId()));
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

                $object = new User();
                $object->setUserId($data['user_id'])
                    ->setUsername($data['username'])
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