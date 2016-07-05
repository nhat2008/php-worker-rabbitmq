<?php
chdir(dirname(__DIR__));

require_once('vendor/autoload.php');
require_once __DIR__ . '/worker/Worker.php';

use Miki\NotificationWorker\Worker;

date_default_timezone_set('UTC');

$jsonStr = file_get_contents(__DIR__ . '/config/config.json');
$config = json_decode($jsonStr);

$worker = new Worker($config);
$worker->listen();