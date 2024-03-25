<?php

/*
 * This file is part of the ViragContainer package.
 *
 * (c) Virag Rajput <codewithvirag@gmail.com>
 *
 * ViragContainer is a lightweight PHP dependency injection container designed to manage object 
 * creation and resolution.
 * It provides a flexible and powerful way to manage dependencies in your PHP projects.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Virag\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class ReflectionContainer extends Container
{
    protected function resolve($concrete)
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            // No constructor, simply instantiate the class
            return new $concrete();
        }

        $parameters = $this->resolveParameters($constructor->getParameters());

        // Create an instance with resolved constructor parameters
        return $reflector->newInstanceArgs($parameters);
    }

    protected function autoWire($key)
    {
        // Attempt to auto-wire if the class exists
        if (class_exists($key)) {
            return $this->resolve($key);
        }

        throw new \Exception("Class not found: {$key}");
    }

    protected function injectMethods($instance)
    {
        $reflector = new ReflectionClass($instance);

        foreach ($reflector->getMethods() as $method) {
            if ($method->isPublic() && !$method->isStatic()) {
                $this->injectMethodDependencies($instance, $method);
            }
        }
    }

    protected function injectMethodDependencies($instance, ReflectionMethod $method)
    {
        $parameters = $this->resolveParameters($method->getParameters());
        $instance->{$method->name}(...$parameters);
    }

    protected function applyInflectors($instance)
    {
        $class = get_class($instance);

        if (isset($this->inflectors[$class])) {
            foreach ($this->inflectors[$class] as $inflector) {
                $inflector($instance);
            }
        }
    }

    protected function resolveParameterDefaultValue(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isOptional() || $parameter->allowsNull()) {
            return null;
        }

        throw new \Exception("Unresolvable dependency for parameter '{$parameter->getName()}'");
    }

    public function extend($abstract, callable $callback)
    {
        if (!isset($this->customResolvers[$abstract])) {
            throw new \Exception("No custom resolver registered for '{$abstract}'");
        }

        $this->customResolvers[$abstract] = function ($container) use ($abstract, $callback) {
            return $callback($this->customResolvers[$abstract]($container), $container);
        };
    }

    public function resolved($abstract)
    {
        return isset($this->resolvedInstances[$abstract]);
    }

    public function flush()
    {
        $this->resolvedInstances = [];
    }
}
