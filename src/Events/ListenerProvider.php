<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Events;

use Closure;
use InvalidArgumentException;
use RuntimeException;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

use Caldera\Events\Listener\CallableListener;
use Caldera\Events\Listener\ListenerInterface;

class ListenerProvider implements ListenerProviderInterface {

    /**
     * Listeners array
     */
    protected array $listeners = [];

    /**
     * ContainerInterface implementation
     */
    protected ?ContainerInterface $container;

    /**
     * Constructor
     * @param ContainerInterface $container ContainerInterface implementation
     */
    public function __construct(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * @param string $type
     * @param mixed  $handler
     */
    public function add(string $type, mixed $handler): self {
        if ($handler instanceof Closure) {
            $handler = new CallableListener($handler);
        }
        if ( !is_string($handler) && !$handler instanceof ListenerInterface ) {
            throw new InvalidArgumentException('The handler must implement ListenerInterface');
        }
        $this->listeners[$type][] = $handler;
        return $this;
    }

    /**
     * @param string $type
     */
    public function clear(string $type): self {
        if (array_key_exists($type, $this->listeners)) {
            unset($this->listeners[$type]);
        }
        return $this;
    }

    /**
     * Get registered listeners for the event type
     * @param  object $event Event object
     */
    public function getListenersForEvent(object $event): iterable {
        $type = get_class($event);
        $listeners = $this->listeners[$type] ?? [];
        $listeners = array_map(function($listener) {
            if ( is_string($listener) ) {
                if (! $this->container ) {
                    throw new RuntimeException('The container is not set');
                }
                if (! $this->container->has($listener) ) {
                    throw new RuntimeException("Unknown listener class: '{$listener}'");
                }
                $listener = $this->container->get($listener);
            }
            return $listener;
        }, $listeners);
        return $listeners;
    }
}
