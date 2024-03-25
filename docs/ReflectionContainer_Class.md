## ReflectionContainer Class Examples.

Here are some possible examples of using the `ReflectionContainer` class:

1. **Basic Usage:**

```php
use Virag\Container\ReflectionContainer;

// Create a new reflection container instance
$container = new ReflectionContainer();

// Resolve and instantiate a class using reflection
$instance = $container->make(MyClass::class);
```

2. **Injecting Dependencies into Constructor:**

```php
use Virag\Container\ReflectionContainer;

class DatabaseConnection
{
    public function __construct($host, $username, $password)
    {
        // Constructor code
    }
}

// Create a new reflection container instance
$container = new ReflectionContainer();

// Bind dependencies
$container->bind('host', 'localhost');
$container->bind('username', 'root');
$container->bind('password', 'password');

// Resolve and instantiate the DatabaseConnection class
$connection = $container->make(DatabaseConnection::class);
```

3. **Extending Resolved Instances:**

```php
use Virag\Container\ReflectionContainer;

class Logger
{
    protected $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function log($message)
    {
        echo "{$this->log}: {$message}\n";
    }
}

// Create a new reflection container instance
$container = new ReflectionContainer();

// Bind a logger with a default log
$container->bind('logger', function ($container) {
    return new Logger('Main Log');
});

// Extend the logger to add additional functionality
$container->extend('logger', function ($logger, $container) {
    $logger->log("Logger extended");
    return $logger;
});

// Resolve and instantiate the logger
$logger = $container->make('logger');
```

4. **Checking if an Instance is Resolved:**

```php
use Virag\Container\ReflectionContainer;

// Create a new reflection container instance
$container = new ReflectionContainer();

// Check if an instance is resolved
$isResolved = $container->resolved(MyClass::class);

if ($isResolved) {
    echo "MyClass has been resolved\n";
} else {
    echo "MyClass has not been resolved\n";
}
```

5. **Flushing Resolved Instances:**

```php
use Virag\Container\ReflectionContainer;

// Create a new reflection container instance
$container = new ReflectionContainer();

// Resolve and instantiate some instances
$instance1 = $container->make(MyClass1::class);
$instance2 = $container->make(MyClass2::class);

// Check if instances are resolved
echo $container->resolved(MyClass1::class) ? "MyClass1 has been resolved\n" : "MyClass1 has not been resolved\n";
echo $container->resolved(MyClass2::class) ? "MyClass2 has been resolved\n" : "MyClass2 has not been resolved\n";

// Flush resolved instances
$container->flush();

// Check if instances are resolved after flushing
echo $container->resolved(MyClass1::class) ? "MyClass1 has been resolved\n" : "MyClass1 has not been resolved\n";
echo $container->resolved(MyClass2::class) ? "MyClass2 has been resolved\n" : "MyClass2 has not been resolved\n";
```

6. **Use With setDelegateContainer Method**


```php
use Virag\Container\Container;
use Virag\Container\ReflectionContainer;

// Create a new main container instance
$mainContainer = new Container();

// Create a new reflection container instance
$reflectionContainer = new ReflectionContainer();

// Set the reflection container as the delegate container for the main container. This allows the main container to delegate resolution of dependencies to the reflection container
$mainContainer->setDelegateContainer($reflectionContainer);

// Resolve and instantiate a class using the main container. The main container will now utilize the reflection container for dependency resolution.
$instance = $mainContainer->make(MyClass::class);
```

These examples demonstrate different ways to utilize the `ReflectionContainer` class for dependency injection, extending resolved instances, checking if instances are resolved, and flushing resolved instances.
