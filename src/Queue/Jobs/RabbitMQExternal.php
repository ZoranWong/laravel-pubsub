<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamilyme\LaravelPubSub\Queue\Jobs;

use Chocofamilyme\LaravelPubSub\Queue\CallQueuedHandler;
use Chocofamilyme\LaravelPubSub\Listeners\EventRouter;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use PhpAmqpLib\Message\AMQPMessage;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

/**
 * Class ListenerMQJob
 *
 * Запускает обработку сообщения опредеденным классом Laravel Listeners
 *
 * @package Chocofamilyme\LaravelPubSub\Queue\Jobs
 */
class RabbitMQExternal extends RabbitMQLaravel
{
    /**
     * @var EventRouter
     */
    protected $eventRouter;

    /** @var CallQueuedHandler  */
    protected $queueHandler;

    /**
     * ListenerMQJob constructor.
     *
     * @param Container         $container
     * @param RabbitMQQueue     $rabbitmq
     * @param AMQPMessage       $message
     * @param string            $connectionName
     * @param string            $queue
     * @param EventRouter       $eventRouter
     * @param CallQueuedHandler $queueHandler
     */
    public function __construct(
        Container $container,
        RabbitMQQueue $rabbitmq,
        AMQPMessage $message,
        string $connectionName,
        string $queue,
        EventRouter $eventRouter,
        CallQueuedHandler $queueHandler
    ) {
        parent::__construct(
            $container,
            $rabbitmq,
            $message,
            $connectionName,
            $queue
        );

        $this->eventRouter  = $eventRouter;
        $this->queueHandler = $queueHandler;
    }

    /**
     * @throws \Chocofamilyme\LaravelPubSub\Exceptions\NotFoundListenerException
     */
    public function fire()
    {
        $payload = $this->payload();
        $listeners = $this->eventRouter->getListeners($this->getName());

        foreach ($listeners as $listener) {
            $this->queueHandler->call($this, $listener, $payload);
        }
    }

    /**
     * Get the name of the queued job class.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->payload()['_event'] ?? Arr::get($this->message->delivery_info, 'routing_key');
    }
}
