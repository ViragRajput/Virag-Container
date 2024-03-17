<?php

namespace ViragContainer\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Container
{
    protected $bindings = [];
    protected $aliases = [];
    protected $shared = [];
    protected $serviceProviders = [];
    protected $inflectors = [];
    protected $autoWire = true;
    protected $resolvedInstances = [];
    protected $delegateContainer;
    protected $config = [];
    protected $customResolvers = [];
    protected $tags = [];
    protected $sharedInstances = [];
    protected $closures = [];
    protected $contextualBindings = [];
    protected $currentContextualBinding = '';
    protected $factories = [];

    public function bind(string $key, $concrete, $shared = false)
    {
        if ($concrete instanceof \Closure) {
            $this->closures[$key] = $concrete;
        } else {
            $this->bindings[$key] = ['concrete' => $concrete, 'shared' => $shared];
        }
    }

    public function singleton(string $key, $concrete)
    {
        $this->bind($key, $concrete, true);
    }

    public function bindConfig(string $key, $value)
    {
        $this->config[$key] = $value;
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function make(string $key, array $context = [])
    {
        $realKey = $this->resolveAlias($key);

        // Check if the key is bound to a string
        if (isset($this->bindings[$realKey]) && is_string($this->bindings[$realKey]['concrete'])) {
            return $this->bindings[$realKey]['concrete'];
        }

        // Check for contextual binding
        if (isset($this->contextualBindings[$realKey][$context])) {
            $concrete = $this->contextualBindings[$realKey][$context];
            return $this->resolve($concrete);
        }

        // Check for shared instance
        if (isset($this->resolvedInstances[$realKey])) {
            return $this->resolvedInstances[$realKey];
        }

        // Check for custom resolver
        if (isset($this->customResolvers[$realKey])) {
            $resolver = $this->customResolvers[$realKey];
            $instance = $resolver($this);

            if (isset($this->bindings[$realKey]['shared']) && $this->bindings[$realKey]['shared']) {
                $this->resolvedInstances[$realKey] = $instance;
            }

            return $instance;
        }

        // Check for closure binding
        if (isset($this->closures[$realKey])) {
            $closure = $this->closures[$realKey];
            $instance = $closure($this);

            if (isset($this->bindings[$realKey]['shared']) && $this->bindings[$realKey]['shared']) {
                $this->resolvedInstances[$realKey] = $instance;
            }

            return $instance;
        }

        // Check for regular binding
        if (isset($this->bindings[$realKey])) {
            $binding = $this->bindings[$realKey];
            $concrete = $binding['concrete'];
            $shared = $binding['shared'];

            $instance = $shared ? $this->getShared($realKey, $concrete) : $this->resolve($concrete);
            $this->resolvedInstances[$realKey] = $instance;

            return $instance;
        }

        // Delegate to the parent container if available
        if ($this->delegateContainer && $this->delegateContainer->has($realKey)) {
            return $this->delegateContainer->make($realKey, $context);
        }

        // Auto-wire if enabled
        if ($this->autoWire) {
            return $this->autoWire($realKey);
        }

        // Handle not found case, for now, let's throw an exception
        throw new \Exception("Binding not found for key: {$realKey}");
    }

    public function has(string $key)
    {
        $realKey = $this->resolveAlias($key);
        return isset($this->bindings[$realKey]) || ($this->delegateContainer && $this->delegateContainer->has($realKey));
    }

    public function setDelegateContainer(Container $container)
    {
        $this->delegateContainer = $container;
    }

    public function addServiceProvider(string $providerClass)
    {
        $provider = new $providerClass($this);
        $this->serviceProviders[] = $provider;
        $provider->register();
    }

    public function addInflector(string $interface, callable $inflector)
    {
        $this->inflectors[$interface][] = $inflector;
    }

    public function enableAutoWiring()
    {
        $this->autoWire = true;
    }

    public function disableAutoWiring()
    {
        $this->autoWire = false;
    }

    public function resolveWith(string $key, $instance)
    {
        $realKey = $this->resolveAlias($key);
        $this->resolvedInstances[$realKey] = $instance;
    }

    public function resolveMethod($instance, string $methodName)
    {
        if (!method_exists($instance, $methodName)) {
            throw new \Exception("Method {$methodName} not found in class " . get_class($instance));
        }

        $method = new ReflectionMethod($instance, $methodName);
        $parameters = $this->resolveParameters($method->getParameters());

        return $method->invokeArgs($instance, $parameters);
    }

    public function alias(string $key, string $alias)
    {
        $this->aliases[$alias] = $key;
    }

    public function registerCustomResolver(string $key, callable $resolver)
    {
        $this->customResolvers[$key] = $resolver;
    }

    public function tag(string $tag, array $services)
    {
        foreach ($services as $service) {
            $this->tags[$tag][] = $service;
        }
    }

    public function getTagged(string $tag)
    {
        return $this->tags[$tag] ?? [];
    }

    public function share(string $key, $instance)
    {
        $this->sharedInstances[$key] = $instance;
    }

    public function getSharedInstance(string $key)
    {
        return $this->sharedInstances[$key] ?? null;
    }

    public function when(string $key, callable $callback)
    {
        $this->currentContextualBinding = $this->resolveAlias($key);
        $callback($this);
        $this->currentContextualBinding = null;
    }

    public function needs(string $abstract, $implementation)
    {
        $abstract = $this->resolveAlias($abstract);
        $implementation = $this->resolveAlias($implementation);

        $this->contextualBindings[$this->currentContextualBinding][$abstract] = $implementation;
    }

    protected function resolveAlias(string $key)
    {
        return $this->aliases[$key] ?? $key;
    }

    protected function resolve($concrete)
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            // Handle non-instantiable classes
            throw new \Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            // No constructor, simply instantiate the class
            return new $concrete();
        }

        $parameters = $this->resolveParameters($constructor->getParameters());

        // Create an instance with resolved constructor parameters
        $instance = $reflector->newInstanceArgs($parameters);

        // Inject dependencies into methods
        $this->injectMethods($instance);

        // Apply inflectors
        $this->applyInflectors($instance);

        return $instance;
    }

    protected function resolveParameters(array $parameters)
    {
        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            if ($parameterClass = $parameter->getClass()) {
                // Recursively resolve constructor parameters
                $resolvedParameters[] = $this->make($parameterClass->getName());
            } else {
                // No type hint, use default value or null
                $resolvedParameters[] = $this->resolveParameterDefaultValue($parameter);
            }
        }

        return $resolvedParameters;
    }

    protected function resolveParameterDefaultValue(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        return null; // No default value, return null
    }

    protected function getShared($key, $concrete)
    {
        if (!isset($this->shared[$key])) {
            $this->shared[$key] = $this->resolve($concrete);
        }

        return $this->shared[$key];
    }

    protected function injectMethods($instance)
    {
        $reflector = new ReflectionClass($instance);

        foreach ($reflector->getMethods() as $method) {
            $parameters = $this->resolveParameters($method->getParameters());
            $instance->{$method->name}(...$parameters);
        }
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

    protected function autoWire($key)
    {
        // Attempt to auto-wire if the class exists
        if (class_exists($key)) {
            return $this->resolve($key);
        }

        // Handle not found case, for now, let's throw an exception
        throw new \Exception("Class not found: {$key}");
    }

    // Resolving Instance using factories
    public function bindFactory(string $key, callable $factory, $shared = false)
    {
        $this->factories[$key] = ['factory' => $factory, 'shared' => $shared];
    }

    public function singletonFactory(string $key, callable $factory)
    {
        $this->bindFactory($key, $factory, true);
    }

    public function factory(string $key, array $parameters = [])
    {
        $realKey = $this->resolveAlias($key);

        // Check for shared instance
        if (isset($this->resolvedInstances[$realKey])) {
            return $this->resolvedInstances[$realKey];
        }

        // Check for custom resolver
        if (isset($this->customResolvers[$realKey])) {
            $resolver = $this->customResolvers[$realKey];
            $instance = $resolver($this);

            if (isset($this->bindings[$realKey]['shared']) && $this->bindings[$realKey]['shared']) {
                $this->resolvedInstances[$realKey] = $instance;
            }

            return $instance;
        }

        // Check for closure binding
        if (isset($this->closures[$realKey])) {
            $closure = $this->closures[$realKey];
            $instance = $closure($this);

            if (isset($this->bindings[$realKey]['shared']) && $this->bindings[$realKey]['shared']) {
                $this->resolvedInstances[$realKey] = $instance;
            }

            return $instance;
        }

        // Check for factory binding
        if (isset($this->factories[$realKey])) {
            $factory = $this->factories[$realKey]['factory'];
            $shared = $this->factories[$realKey]['shared'];

            $instance = $factory($this, ...$parameters);

            if ($shared) {
                $this->resolvedInstances[$realKey] = $instance;
            }

            return $instance;
        }

        // Check for regular binding
        if (isset($this->bindings[$realKey])) {
            $binding = $this->bindings[$realKey];
            $concrete = $binding['concrete'];
            $shared = $binding['shared'];

            $instance = $shared ? $this->getShared($realKey, $concrete) : $this->resolve($concrete);
            $this->resolvedInstances[$realKey] = $instance;

            return $instance;
        }

        // Delegate to the parent container if available
        if ($this->delegateContainer && $this->delegateContainer->has($realKey)) {
            return $this->delegateContainer->factory($realKey, $parameters);
        }

        // Auto-wire if enabled
        if ($this->autoWire) {
            return $this->autoWire($realKey);
        }

        // Handle not found case, for now, let's throw an exception
        throw new \Exception("Binding not found for key: {$realKey}");
    }
}
