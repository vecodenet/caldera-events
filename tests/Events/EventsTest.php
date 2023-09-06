<?php

declare(strict_types = 1);

/**
 * Caldera Events
 * Event dispatcher implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Tests\Events;

use InvalidArgumentException;
use RuntimeException;

use PHPUnit\Framework\TestCase;

use Caldera\Container\Container;
use Caldera\Events\Event\StoppableEvent;
use Caldera\Events\EventDispatcher;
use Caldera\Events\Listener\ListenerInterface;
use Caldera\Events\ListenerProvider;

class EventDispatcherTest extends TestCase {

    public function testDispatch() {
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, function(TestEvent $event) {
            $this->assertInstanceOf(TestEvent::class, $event);
            $this->assertEquals('foo', $event->getValue());
        });
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $dispatcher->dispatch($event);
    }

    public function testClearListeners() {
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, function(TestEvent $event) {
            $this->fail('This should not occur');
        });
        $listener_provider->clear(TestEvent::class);
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $dispatcher->dispatch($event);
        $this->assertTrue(true);
    }

    public function testStoppableEvent() {
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, function(TestEvent $event) {
            $event->stopPropagation();
            $this->assertTrue(true);
        });
        $listener_provider->add(TestEvent::class, function(TestEvent $event) {
            $this->fail('This should not occur');
        });
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $dispatcher->dispatch($event);
    }

    public function testListener() {
        $listener = new TestListener($this);
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, $listener);
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $dispatcher->dispatch($event);
    }

    public function testInvalidListener() {
        $this->expectException(InvalidArgumentException::class);
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, $this);
    }

    public function testListenerProviderUsingContainer() {
        $test = $this;
        $container = new Container();
        $listener_provider = new ListenerProvider($container);
        $container->add(TestListener::class, true, function() use ($test) {
            return new TestListener($test);
        });
        $listener_provider->add(TestEvent::class, TestListener::class);
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $dispatcher->dispatch($event);
    }

    public function testListenerProviderUsingContainerWithoutContainer() {
        $listener_provider = new ListenerProvider();
        $listener_provider->add(TestEvent::class, TestListener::class);
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $this->expectException(RuntimeException::class, 'The container is not set');
        $dispatcher->dispatch($event);
    }

    public function testListenerProviderUsingContainerAndInvalidListener() {
        $container = new Container();
        $listener_provider = new ListenerProvider($container);
        $listener_provider->add(TestEvent::class, TestListener::class);
        $dispatcher = new EventDispatcher($listener_provider);
        $event = new TestEvent('foo');
        $this->expectException(RuntimeException::class, "Unknown listener class 'TestListener'");
        $dispatcher->dispatch($event);
    }
}

class TestEvent extends StoppableEvent {

    protected string $value;

    function __construct(string $value) {
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }
}

class TestListener implements ListenerInterface {

    protected TestCase $test;

    public function __construct(TestCase $test) {
        $this->test = $test;
    }

    public function handle(object $event): void {
        $this->test->assertInstanceOf(TestEvent::class, $event);
        $this->test->assertTrue(true);
    }
}
