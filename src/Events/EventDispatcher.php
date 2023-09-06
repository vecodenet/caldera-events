<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface {

    /**
     * ListenerProviderInterface implementation
     */
    protected ListenerProviderInterface $listener_provider;

    /**
     * Constructor
     * @param ListenerProviderInterface $listener_provider ListenerProviderInterface implementation
     */
    public function __construct(ListenerProviderInterface $listener_provider) {
        $this->listener_provider = $listener_provider;
    }

    /**
     * Dispatch event
     * @param  object $event Event object
     */
    public function dispatch(object $event): object {
        $listeners = $this->listener_provider->getListenersForEvent($event);
        foreach ($listeners as $listener) {
            if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
                break;
            }
            $listener->handle($event);
        }
        return $event;
    }
}
