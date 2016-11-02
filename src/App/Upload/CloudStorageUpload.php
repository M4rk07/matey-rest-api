<?php

namespace App\Upload;
use App\Paths\Paths;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 2.11.16.
 * Time: 16.28
 */
class CloudStorageUpload
{

    public $imgPath;
    public $imgName;

    public function __construct($imgPath, $imgName) {
        $this->imgPath = $imgPath;
        $this->imgName = $imgName;
    }

    public function run() {
        $client = new \Google_Client();
        $client->setScopes(\Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
        $client->useApplicationDefaultCredentials();

        /**
         * Upload a file to google cloud storage
         */
        $storage = new \Google_Service_Storage($client);
        $file_name = $this->imgName;
        $obj = new \Google_Service_Storage_StorageObject();
        $obj->setName($file_name);

        $storage->objects->insert(
            Paths::BUCKET_MATEY,
            $obj,
            ['name' => $file_name, 'data' => file_get_contents($this->imgPath), 'uploadType' => 'media', 'predefinedAcl' => 'publicRead']
        );
    }

}