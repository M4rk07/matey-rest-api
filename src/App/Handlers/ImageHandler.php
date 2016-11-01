<?php

namespace App\Handlers;
use App\Services\CloudStorageService;
use google\appengine\api\app_identity\AppIdentityService;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 31.10.16.
 * Time: 15.47
 */
class ImageHandler
{

    public function handleFacebookImage ($fbId, $imgName) {

        $cloudStorage = new CloudStorageService();

        $cloudStorage->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=100&height=100", $imgName, 'profile_pictures/100x100');
        $cloudStorage->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=200&height=200", $imgName, 'profile_pictures/200x200');
        $cloudStorage->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=320&height=320", $imgName, 'profile_pictures/320x320');
        $cloudStorage->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=480&height=480", $imgName, 'profile_pictures/480x480');

    }




}