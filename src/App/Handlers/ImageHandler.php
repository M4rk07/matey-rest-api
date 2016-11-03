<?php

namespace App\Handlers;
use App\MateyModels\User;
use App\Services\CloudStorageService;
use App\Upload\CloudStorageUpload;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use google\appengine\api\app_identity\AppIdentityService;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 31.10.16.
 * Time: 15.47
 */
class ImageHandler
{

    public function handleFacebookProfilePicture (User $user) {

        $img1 = file_get_contents("http://graph.facebook.com/".$user->getFbId()."/picture?width=100&height=100");
        $img2 = file_get_contents("http://graph.facebook.com/".$user->getFbId()."/picture?width=200&height=200");
        $img3 = file_get_contents("http://graph.facebook.com/".$user->getFbId()."/picture?width=480&height=480");
        $img4 = file_get_contents("http://graph.facebook.com/".$user->getFbId()."/picture?width=720&height=720");

        $uploads = array(
            array(
                'file' => $img1,
                'name' => 'profile_pictures/100x100/'.$user->getUserId().'.jpg'
            ),
            array(
                'file' => $img2,
                'name' => 'profile_pictures/200x200/'.$user->getUserId().'.jpg'
            ),
            array(
                'file' => $img3,
                'name' => 'profile_pictures/480x480/'.$user->getUserId().'.jpg'
            ),
            array(
                'file' => $img4,
                'name' => 'profile_pictures/720x720/'.$user->getUserId().'.jpg'
            )
        );

        $cloudStorage1 = new CloudStorageUpload($uploads);
        $cloudStorage1->run();

    }




}