<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 14.33
 */

namespace App\Upload;


use Aws\S3\S3Client;
use Aws\Sdk;

class S3Storage
{
    private $uploads;

    public function __construct(array $uploads) {
        $this->uploads = $uploads;
    }

    public function upload() {
        $sdk = new Sdk([
            'region'   => 'us-west-2',
            'version'  => 'latest',
            'credentials' => [
                'key'    => '',
                'secret' => ''
            ],
        ]);
        $s3 = $sdk->createS3();

        /**
         * Upload a file to google cloud storage
         */

        $errors = 0;
        $promises = [];
        $i = 0;
        foreach($this->uploads as $upload) {

            $promises[$i] = $s3->putObjectAsync(array(
                'Bucket' => 'matey',
                'Key' => $upload['name'],
                'Body' => $upload['file'],
                'ACL' => 'public-read',
                'Metadata'     => array(
                    'mime' => $upload['mime'],
                    'extension' => $upload['extension'],
                    'filename' => $upload['filename']
                )
            ));

            $promises[$i++]
                ->then(
                    function ($value) {},
                    function ($reason) use ($errors) {
                        // The call failed. You can recover from the error here and
                        // return a value that will be provided to the next successful
                        // then() callback. Let's retry the call.
                        $errors++;
                    }
                );

        }

        $results = \GuzzleHttp\Promise\unwrap($promises);

        return $errors;

    }
}