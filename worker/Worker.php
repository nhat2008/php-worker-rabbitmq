<?php
namespace Miki\NotificationWorker;

require_once __DIR__ . "/../task/PushNotification.php";
use Miki\Task\PushNotification;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use SebastianBergmann\RecursionContext\Exception;

class Worker
{
    private $log;
    private $config;
    private $username;
    private $password;
    private $host;
    private $port;
    private $queue;
    private $exchange;

    public function __construct($config)
    {
        $this->config = $config;
        $this->username = $this->config->RabbitMQ->username;
        $this->password = $this->config->RabbitMQ->password;
        $this->host = $this->config->RabbitMQ->host;
        $this->port = $this->config->RabbitMQ->port;
        $this->queue = $this->config->RabbitMQ->queue;
        $this->exchange = $this->config->RabbitMQ->exchange;
        $this->bindKey = $this->config->RabbitMQ->bind_key;

        $this->log = new Logger('Worker');
        $this->log->pushHandler(new StreamHandler($this->config->Log->folder.'worker.log', Logger::INFO));

    }
    public function listen()
    {
        $this->log->addInfo('Begin listen the queue: ' . $this->queue);

        $connection = new AMQPConnection($this->host, $this->port, $this->username, $this->password);
        $channel = $connection->channel();

        $channel->queue_declare(
            $this->queue,       #queue
            false,              #passive
            true,               #durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false,              #exclusive - queues may only be accessed by the current connection
            false               #auto delete - the queue is deleted when all consumers have finished using it
        );

        $channel->exchange_declare(
            $this->exchange,
            'fanout',
            false,
            false,
            false
        );

        $channel->queue_bind($this->queue, $this->exchange, $this->bindKey);

        /**
         * indicate interest in consuming messages from a particular queue. When they do
         * so, we say that they register a consumer or, simply put, subscribe to a queue.
         * Each consumer (subscription) has an identifier called a consumer tag
         */
        $channel->basic_consume(
            $this->queue,           #queue
            '',                     #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,                  #no local - TRUE: the server will not send messages to the connection that published them
            false,                  #no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,                  #exclusive - queues may only be accessed by the current connection
            false,                  #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            array($this, 'process') #callback: this will call a function with name is "process"
        );

        while(count($channel->callbacks)) {
            $this->log->addInfo('Waiting for incoming messages');
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * process received request
     *
     * @param AMQPMessage $msg
     */
    public function process(AMQPMessage $msg)
    {
        try {
            $this->sendNotification($msg->body);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }catch(\Exception $e){
            $this->log->addError($e->getMessage());
        }
    }


    /**
     * Sends notification
     *
     * @return Worker
     */
    private function sendNotification($message)
    {
        $result = PushNotification::send($message);
        return $result;
    }
}