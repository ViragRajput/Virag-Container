<?php

namespace ViragContainer\Container;

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
            return new $concrete();
        }

        $parameters = $this->resolveParameters($constructor->getParameters());

        $instance = $reflector->newInstanceArgs($parameters);

        $this->injectMethods($instance);

        $this->applyInflectors($instance);

        return $instance;
    }

    protected function autoWire($key)
    {
        if (class_exists($key)) {
            return $this->resolve($key);
        }
        throw new \Exception("Class not found: {$key}");
    }
}
