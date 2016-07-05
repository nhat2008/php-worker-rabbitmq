<?php
namespace Miki\Util;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class NotificationManager
{

    static $PUSH_NOTIFICATION_LOG_FILE = "userPushNotification.log";

    const iOS = 1;
    const Android = 2;


    /**
     * @return Log File
     */
    private static function _initializeLogger()
    {
        $jsonStr = file_get_contents(__DIR__ . '/../config/config.json');
        $config = json_decode($jsonStr);
        $logger = new Logger('Worker');
        $logger->pushHandler(new StreamHandler($config->Log->folder . date('d-m-Y') . '/' . self::$PUSH_NOTIFICATION_LOG_FILE, Logger::INFO));
        return $logger;
    }

    public static function push($json)
    {
        try {
            $logger = self::_initializeLogger();
            $logger->addInfo('Pushing the notification to appboy');

            $client = new \GuzzleHttp\Client();
            try {
                $promise = $client->postAsync('https://api.appboy.com/messages/send', json_decode($json,true));

                $logger->addInfo("Notification pushed");
                $promise->then(
                    function (ResponseInterface $res) {
                        if ($res->getStatusCode() == 201) {
                            $logger = self::_initializeLogger();
                            $logger->addInfo("Push noti successfully");
                        }
                    },
                    function (RequestException $e) {
                        $logger = self::_initializeLogger();
                        $logger->addInfo($e->getMessage() . ". " . $e->getRequest()->getMethod());
                    }
                );
                $promise->wait();
            } catch (\Exception $e) {
                $logger->addError($e->getResponse()->getBody() . PHP_EOL);
            }
        } catch (\Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die;
        }


    }
}