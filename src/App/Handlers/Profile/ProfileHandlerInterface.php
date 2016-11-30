<?php

namespace App\Handlers\Profile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.21
 */
interface ProfileHandlerInterface
{

    public function getProfile(Request $request, $id);
    public function setProfilePicture(Request $request);
    public function updateProfileData(Request $request);

}