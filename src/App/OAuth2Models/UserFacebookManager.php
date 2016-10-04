<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.10.16.
 * Time: 23.43
 */

namespace App\OAuth2Models;


use App\Services\BaseService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserFacebookManager extends BaseService
{

    public function __construct () {
        parent::__construct();
        $this->tableName = self::T_USER;
        $this->className = 'App\\OAuth2Models\\UserFacebook';
        $this->identifier = "id_user";
    }


    public function loadUserByUsername($username)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".id_user, "
            . self::T_USER . ".email, "
            . self::T_FB_USER . ".fb_id, 
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_FB_USER . " USING(id_user) 
            WHERE " . self::T_USER . ".email = ? LIMIT 1",
            array($username)
        );
        // make objects form result
        $models = $this->setUserCredentialsObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserFacebook $user)
    {
        $all = $this->db->fetchAll(
            "SELECT " . self::T_USER . ".id_user, "
            . self::T_USER . ".email, "
            . self::T_FB_USER . ".fb_id, 
            FROM " . self::T_USER . "
            LEFT JOIN " . self::T_FB_USER . " USING(id_user) 
            WHERE " . self::T_USER . ".id_user = ? LIMIT 1",
            array($user->getId()));
        // make objects form result
        $models = $this->setUserCredentialsObjects($all);

        return is_array($models) ? reset($models) : $models;
    }

    public function supportsClass($class)
    {
        return $this->className === $class
        || is_subclass_of($class, $this->className);
    }

    // making objects form array
    public function setUserCredentialsObjects(array $all)
    {
        $userObjects = [];

        try {

            foreach ($all as $data) {

                $object = new $this->className();
                $object->setUserId($data['id_user'])
                    ->setUsername($data['email']);

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