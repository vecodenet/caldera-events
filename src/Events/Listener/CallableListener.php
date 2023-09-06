<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Events\Listener;

use Closure;

class CallableListener implements ListenerInterface {

    /**
     * Callback
     */
    protected Closure $callback;

    /**
     * Constructor
     * @param Closure $callback Listener callback
     */
    public function __construct(Closure $callback) {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function handle(object $event): void {
        call_user_func($this->callback, $event);
    }
}
