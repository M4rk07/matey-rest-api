<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 22.3.17.
 * Time: 16.27
 */

namespace App\Services;

use Sly\NotificationPusher\Adapter\Apns;
use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

class NotificationService
{

    public function push ($tokens, $message) {
        // API access key from Google API's Console
        define( 'API_ACCESS_KEY', 'AIzaSyDdvRxK8P2-6ZAjFqrA13rBd4qWHuDrWgs' );
        $registrationIds = $tokens;
        // prep the bundle

        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'			=> $message
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;
    }

}