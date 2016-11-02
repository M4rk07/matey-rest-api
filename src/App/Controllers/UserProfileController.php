<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 2.11.16.
 * Time: 00.16
 */

namespace App\Controllers;


use App\Services\CloudStorageService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;

class UserProfileController
{

    public function getProfilePictureAction(Request $request, $user_id) {

        $profilePictureSize = $request->get('profilePicture');
        if(!isset($profilePictureSize)) $profilePictureSize = 'small';

        $cloudStorage = new CloudStorageService();
        $profilePicture = $cloudStorage->generateProfilePictureLink($user_id, $profilePictureSize);

        header("Location: " . $profilePicture);
        exit;

    }

}