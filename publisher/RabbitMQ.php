<?php

namespace Publisher\Helpers;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{
    private static $connection = null;
    private static $channel = null;

    public static function getChannel()
    {
        if (null === static::$channel) {
            if (null === static::$connection) {
                $config = include APPLICATION_PATH . "/config/config.php";
                /* Create connection*/
                static::$connection = new AMQPStreamConnection($config->rabbitmq->server, (int)($config->rabbitmq->port), $config->rabbitmq->username, $config->rabbitmq->password);
            }
            static::$channel = static::$connection->channel();
        }
        return static::$channel;
    }
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    public static function publish_message($body)
    {
        $config = include APPLICATION_PATH . "/config/config.php";

        /*Standazation the message and the properties*/
        $properties = array('content_type' => 'application/json', 'delivery_mode' => 2, 'content_encoding' => 'utf-8');
        $msg = new AMQPMessage($body, $properties);
        
        /*Publish message to Exchange*/
        $channel = self::getChannel();
        $channel->basic_publish($msg, $config->rabbitmq->sync->exchange, $config->rabbitmq->sync->routing_key);
    }

    public static function publish_notification($body)
    {
        $config = include APPLICATION_PATH . "/config/config.php";

        /*Standazation the message and the properties*/
        $properties = array('content_type' => 'application/json', 'delivery_mode' => 2, 'content_encoding' => 'utf-8');
        $msg = new AMQPMessage($body, $properties);

        /*Publish message to Exchange*/
        $channel = self::getChannel();
        $channel->basic_publish($msg, $config->rabbitmq->notification->exchange, $config->rabbitmq->notification->routing_key);
    }

}