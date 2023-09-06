<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Events\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent implements StoppableEventInterface {

    /**
     * Propagation stopped flag
     */
    protected bool $propagation_stopped = false;

    /**
     * Stop event propagation
     */
    public function stopPropagation(): self {
        $this->propagation_stopped = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPropagationStopped() : bool {
        return $this->propagation_stopped;
    }
}
