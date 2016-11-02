<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 1.11.16.
 * Time: 20.56
 */

namespace App\Services;


use App\Paths\Paths;

class CloudStorageService
{

    public function generateProfilePictureLink($userId, $size = 'small') {
        $dimension = '100x100';
        if($size != 'small' && in_array($size, array('medium', 'large', 'veryLarge'))) {
            if($size == 'medium') $dimension = '200x200';
            else if($size == 'large') $dimension = '480x480';
            else if($size == 'veryLarge') $dimension = '720x720';
        }
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/profile_pictures/".$dimension."/".$userId.".jpg";
    }

    public function generateSignedURL($objectName, $method = 'GET', $duration = 1000 ) {
        $expires = time( ) + $duration;
        $content_type = ($method == 'PUT') ? 'application/x-www-form-urlencoded' : '';
        $to_sign = ($method . "\n" .
            /* Content-MD5 */ "\n" .
            $content_type . "\n" .
            $expires . "\n" .
            '/'.Paths::BUCKET_MATEY.'/' . $objectName);

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
        return ('https://storage.googleapis.com/'.Paths::BUCKET_MATEY.'/' .
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