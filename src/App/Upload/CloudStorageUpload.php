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

    public $uploads;

    public function __construct(array $uploads) {
        $this->uploads = $uploads;
    }

    public function run() {
        $client = new \Google_Client();
        $client->setScopes(\Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
        $client->useApplicationDefaultCredentials();

        $storage = new \Google_Service_Storage($client);

        /**
         * Upload a file to google cloud storage
         */

        foreach($this->uploads as $upload) {

            $file_name = $upload['name'];
            $obj = new \Google_Service_Storage_StorageObject();
            $obj->setName($file_name);

            $storage->objects->insert(
                Paths::BUCKET_MATEY,
                $obj,
                ['name' => $file_name, 'data' => $upload['file'], 'uploadType' => 'media', 'predefinedAcl' => 'publicRead']
            );


        }

    }

}