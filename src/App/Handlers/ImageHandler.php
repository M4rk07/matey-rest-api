<?php

namespace App\Handlers;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 31.10.16.
 * Time: 15.47
 */
class ImageHandler
{

    public function handleFacebookImage ($fbId, $imgName) {

        $this->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=100&height=100", $imgName, 'profile_pictures/100x100');
        $this->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=200&height=200", $imgName, 'profile_pictures/200x200');
        $this->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=320&height=320", $imgName, 'profile_pictures/320x320');
        $this->storeImageToCloud("http://graph.facebook.com/".$fbId."/picture?width=480&height=480", $imgName, 'profile_pictures/480x480');

    }

    public function storeImageToCloud($img, $imgName, $folder) {

        $client = new \Google_Client();
        $client->setScopes(\Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
        $client->useApplicationDefaultCredentials();

        /**
         * Upload a file to google cloud storage
         */
        $storage = new \Google_Service_Storage($client);
        $file_name = $folder.'/'.$imgName.".jpg";
        $obj = new \Google_Service_Storage_StorageObject();
        $obj->setName($file_name);

        $storage->objects->insert(
            "matey-148023.appspot.com",
            $obj,
            ['name' => $file_name, 'data' => file_get_contents($img), 'uploadType' => 'media']
        );

    }

}