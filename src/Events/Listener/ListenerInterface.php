<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Events\Listener;

interface ListenerInterface {

    /**
     * Handle the event
     * @param  object $event Event object
     */
    public function handle(object $event): void;
}
