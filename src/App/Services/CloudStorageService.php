<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 1.11.16.
 * Time: 20.56
 */

namespace App\Services;


class CloudStorageService
{

    private $bucketName = 'matey-148023.appspot.com';

    public function storeImageToCloud($imgPath, $imgName, $folder) {

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
            ['name' => $file_name, 'data' => file_get_contents($imgPath), 'uploadType' => 'media', 'predefinedAcl' => 'publicRead']
        );

    }

    function generateSignedURL($objectName, $method = 'GET', $duration = 1000 ) {
        $expires = time( ) + $duration;
        $content_type = ($method == 'PUT') ? 'application/x-www-form-urlencoded' : '';
        $to_sign = ($method . "\n" .
            /* Content-MD5 */ "\n" .
            $content_type . "\n" .
            $expires . "\n" .
            '/'.$this->bucketName.'/' . $objectName);

        $signature = '*Signature will go here*';
        $mateyService = file_get_contents(getenv("GOOGLE_APPLICATION_CREDENTIALS"));
        $mateyService = json_decode($mateyService);
        $priv_key = $mateyService->private_key;
        if (!openssl_sign( $to_sign, $signature, $priv_key, 'sha256' )) {
            error_log( 'openssl_sign failed!' );
            $signature = '<failed>';
        } else {
            $signature = urlencode( base64_encode( $signature ) );
        }
        return ('https://storage.googleapis.com/'.$this->bucketName.'/' .
            $objectName .
            '?GoogleAccessId=' . $mateyService->client_email .
            '&Expires=' . $expires . '&Signature=' . $signature);
    }

    function generatePolicy(){

        $policy = '
         {"expiration": "'.date("c", time()+300).'",
           "conditions": [
                ["starts-with", "key", "" ],
                {"acl": "bucket-owner-read" },
                {"bucket": "BUCKET_NAME"},
                {"success_action_redirect": "http://www.example.com/success_notification.html" },
                ["eq", "Content-Type", "image/jpeg" ],
                ["content-length-range", 0, 1000000]
           ]
         }';

        $policy = base64_encode(trim($policy));

        $fp = fopen("../gcs-key.pem", "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key, "");

        $signSuccess = openssl_sign($policy, $signature, $pkeyid, 'sha256' );
        $openssl_error = openssl_error_string();

        if ($signSuccess) {
            $signature = urlencode( base64_encode( $signature ) );
            global $accessId;
            return $signature;
        }

    }

}