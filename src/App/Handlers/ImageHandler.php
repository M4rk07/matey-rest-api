<?php

namespace App\Handlers;
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

    public function handleFacebookProfilePicture ($fbId, $imgName) {

        $cloudStorage1 = new CloudStorageUpload("http://graph.facebook.com/".$fbId."/picture?width=100&height=100", 'profile_pictures/100x100/'.$imgName.'.jpg');
        $cloudStorage2 = new CloudStorageUpload("http://graph.facebook.com/".$fbId."/picture?width=200&height=200", 'profile_pictures/200x200/'.$imgName.'.jpg');
        $cloudStorage3 = new CloudStorageUpload("http://graph.facebook.com/".$fbId."/picture?width=480&height=480", 'profile_pictures/480x480/'.$imgName.'.jpg');
        $cloudStorage4 = new CloudStorageUpload("http://graph.facebook.com/".$fbId."/picture?width=720&height=720", 'profile_pictures/720x720/'.$imgName.'.jpg');
        $cloudStorage1->run();
        $cloudStorage2->run();
        $cloudStorage3->run();
        $cloudStorage4->run();

    }




}