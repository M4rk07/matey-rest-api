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
        // First, instantiate the manager and declare an adapter.
        $pushManager    = new PushManager();
        $adapter = new Gcm();
        /*
        $adapter = new Gcm(array(
            'apiKey' =>
        ));
        */

        $devices = array();
        if(is_array($tokens)) {
            foreach ($tokens as $token) {
                $devices[] = new Device($token);
            }
        } else $devices[] = new Device($tokens);

        // Set the device(s) to push the notification to.
        $devices = new DeviceCollection($devices);

        // Then, create the push skel.
        $message = new Message($message);

        // Finally, create and add the push to the manager, and push it!
        $push = new Push($adapter, $devices, $message);
        $pushManager->add($push);
        $pushManager->push();
    }

}