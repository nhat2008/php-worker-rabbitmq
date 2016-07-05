<?php
/**
 * Nhatnm
 * 09/03/2016
 */
namespace Miki\Task;

require_once __DIR__ . "/../util/NotificationManager.php";
use Miki\Util\NotificationManager;

class PushNotification
{
    public static function send($json){
        $result = NotificationManager::push($json);
        return $result;
    }
}