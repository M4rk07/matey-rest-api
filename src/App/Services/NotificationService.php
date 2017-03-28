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

    public function push ($notificationData) {
        // API access key from Google API's Console
        define( 'API_ACCESS_KEY', 'AAAAjZuDvCk:APA91bGIN-O4936zAKlhDvglXBmnuHN7LoZc5rp3H385zqd-OK_qPcKNKwRWFW_PxmsC1503-av4HEZZWNrg99vVz1Ac82qTXXKKU9Pca4HaSQM3r7WV8dYISCztzGQfnwV77Z7soNZy');
        // prep the bundle

        $data['data'] = $notificationData['data'];
        $fields = array
        (
            'registration_ids' 	=> $notificationData['tokens'],
            'data'			=> $data
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
    }

}