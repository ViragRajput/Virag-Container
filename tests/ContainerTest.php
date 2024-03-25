<?php


use Virag\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testSingletonBindingAndResolution()
    {
        $container = new Container();

        // Bind a class as a singleton
        $container->singleton('foo', Foo::class);

        // Resolve the class from the container multiple times
        $instance1 = $container->make('foo');
        $instance2 = $container->make('foo');

        // Verify that the same instance is returned every time
        $this->assertInstanceOf(Foo::class, $instance1); // Updated assertion
        $this->assertInstanceOf(Foo::class, $instance2); // Updated assertion
        $this->assertSame($instance1, $instance2);
    }
}

class Foo
{
    // Class for testing purposes
}
