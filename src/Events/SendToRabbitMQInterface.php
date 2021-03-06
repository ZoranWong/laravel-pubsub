<?php

namespace Chocofamilyme\LaravelPubSub\Events;

interface SendToRabbitMQInterface
{
    /**
     * Get exchange where to publish the message
     *
     *
     * @return string|null
     */
    public function getExchange(): ?string;

    /**
     * Get exchange type for the rabbitmq event
     *
     * override this method in your event if you want non default exchange type
     *
     * @return string
     */
    public function getExchangeType(): string;

    /**
     * Get routing key, where the message will be routed
     *
     * @return string
     */
    public function getRoutingKey(): string;

    public function getHeaders(): array;

    public function getName(): string;

    public function getEventId(): string;

    public function getEventCreatedAt(): string;
}
