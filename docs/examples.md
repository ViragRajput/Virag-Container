## Example Uses Of ViragContainer Package

### 1. Basic Binding and Resolution
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding a class to the container
$container->bind('Logger', Logger::class);

// Resolving the bound class
$logger = $container->make('Logger');
```
In this example, we create a new container instance, bind the Logger class to the container, and then resolve it using the make method. This demonstrates the fundamental process of binding dependencies to the container and resolving them when needed.

### 2. Singleton Binding
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding a singleton class to the container
$container->singleton('Config', Config::class);

// Resolving the singleton instance
$config = $container->make('Config');
```
In this example, we create a new container instance and bind the Config class as a singleton to the container using the singleton method. Later, when we resolve the Config class using the make method, the container will always return the same instance of Config, ensuring that only one instance of Config is ever created and reused throughout the application.

### 3. Binding with Custom Resolver
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding with a custom resolver
$container->registerCustomResolver('Db', function($container) {
    return new DbConnection($container->make('Config'));
});

// Resolving the bound class using the custom resolver
$dbConnection = $container->make('Db');
```
In this example, we define a custom resolver for the Db class using the registerCustomResolver method. The custom resolver is a closure that receives the container instance as a parameter. Inside the closure, we manually resolve the Config class from the container and use it to instantiate the DbConnection class. Finally, when we resolve the Db class using the make method, the custom resolver is invoked, and the DbConnection instance is returned.

### 4. Contextual Binding
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Contextual binding based on an interface
$container->when('Mailer', function($container) {
    $container->needs('MailerInterface', SmtpMailer::class);
});

// Resolving the bound class
$mailer = $container->make('Mailer');
```
In this example, we use the `when` method to define contextual binding for the `Mailer` class. We specify that when resolving the `Mailer` class, it should resolve to the `SmtpMailer` class, which implements the `MailerInterface`. When we call `make('Mailer')`, the container resolves the `Mailer` class according to the defined contextual binding, and an instance of `SmtpMailer` is returned.

### 5. Binding with Factory
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding with a factory
$container->bindFactory('HttpClient', function($container) {
    return new HttpClient($container->make('Config')->get('api_key'));
});

// Resolving the instance using the factory
$http = $container->factory('HttpClient');
```
In this example, we use the `bindFactory` method to bind the `HttpClient` class to a factory closure. When we call `factory('HttpClient')`, the container resolves the instance using the factory closure, which creates a new instance of `HttpClient` with the appropriate configuration.

### 6. Auto Wiring

```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Enable auto wiring
$container->enableAutoWiring();

// Resolving a class using auto wiring
$service = $container->make(MyService::class);
```
In this example, we enable auto wiring by calling the `enableAutoWiring` method on the container instance. Then, when we call `make(MyService::class)`, the container automatically resolves the dependencies of `MyService` using reflection and instantiates the class accordingly.


### 7. Adding Service Providers
```php
use Virag\Container\Container;
use App\Providers\DatabaseProvider;

// Create a new container instance
$container = new Container();

// Add a service provider
$container->addServiceProvider(DatabaseProvider::class);

// Resolving a service provided by the added provider
$database = $container->make('Database');
```
In this example, we create a new container instance and add a service provider `DatabaseProvider` using the `addServiceProvider` method. The service provider is responsible for registering services with the container. After adding the provider, we can resolve services provided by it using the `make` method. Here, we resolve a service named `'Database'` provided by the `DatabaseProvider`.

### 8. Tagging Services
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Tagging multiple services
$container->tag('logging', ['Logger', 'LogWriter']);

// Retrieving tagged services
$services = $container->getTagged('logging');
```

### 9. Using Inflectors
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Adding an inflector
$container->addInflector('ServiceInterface', function($service) use ($logger) {
    $service->setLogger($logger);
});

// Resolving a service that implements ServiceInterface
$service = $container->make('ConcreteService');
```

### 10. Sharing Instances
```php
use Virag\Container\Container;
use App\Connection\DatabaseConnection;

// Create a new container instance
$container = new Container();

// Resolving and sharing an instance
$container->share('DatabaseConnection', new DatabaseConnection('localhost'));

// Retrieving the shared instance
$connection = $container->getSharedInstance('DatabaseConnection');
```

### 11. Alias Binding
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Alias binding
$container->alias('DB', 'Database');

// Resolving a service using alias
$database = $container->make('DB');
```

### 12. Binding Configuration Values
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding configuration values
$container->bindConfig('app.debug', true);

// Getting configuration values
$debugMode = $container->getConfig('app.debug');
```

### 13. Resolving With Custom Context
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding classes
$container->bind('Mailer', SmtpMailer::class);
$container->bind('Logger', FileLogger::class);

// Resolving with custom context
$mailerForUser = $container->make('Mailer', ['user' => 'John']);
$mailerForAdmin = $container->make('Mailer', ['user' => 'Admin']);
```

### 14. Conditional Binding
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Conditional binding
$container->when('Mailer', function($container) {
    $container->needs('MailerInterface', SmtpMailer::class)->when(function($context) {
        return $context['user'] === 'John';
    });
    $container->needs('MailerInterface', MailgunMailer::class)->when(function($context) {
        return $context['user'] === 'Admin';
    });
});

// Resolving based on conditions
$mailerForUser = $container->make('Mailer', ['user' => 'John']);
$mailerForAdmin = $container->make('Mailer', ['user' => 'Admin']);
```

### 15. Using Factories with Parameters
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Factory binding with parameters
$container->bindFactory('HttpClient', function($container, $url) {
    return new HttpClient($url, $container->make('Logger'));
});

// Resolving with factory and parameters
$http = $container->factory('HttpClient', ['http://example.com']);
```

### 16. Dynamic Binding Configuration
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Dynamic binding configuration
$config = [
    'Logger' => FileLogger::class,
    'Storage' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'password'
    ]
];

foreach ($config as $key => $value) {
    if (is_array($value)) {
        $container->bind($key, function($container) use ($value) {
            return new DatabaseStorage($value['driver'], $value['host'], $value['username'], $value['password']);
        });
    } else {
        $container->bind($key, $value);
    }
}

// Resolving dynamically configured bindings
$logger = $container->make('Logger');
$storage = $container->make('Storage');
```

### 17. Using Shared Instances with Context
```php
use Virag\Container\Container;
use App\Connection\DatabaseConnection;

// Create a new container instance
$container = new Container();

// Shared instance with context
$container->share('DatabaseConnection', function($container) {
    return new DatabaseConnection($container->make('Config')->get('db.host'));
});

// Resolving shared instance with context
$connectionForWrite = $container->getSharedInstance('DatabaseConnection');
$connectionForRead = $container->getSharedInstance('DatabaseConnection');
```

### 18. Advanced Contextual Binding
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Advanced contextual binding
$container->when('Mailer', function($container) {
    $container->needs('MailerInterface', SmtpMailer::class)->give('John', SmtpMailer::class);
    $container->needs('MailerInterface', MailgunMailer::class)->give('Admin', MailgunMailer::class);
});

// Resolving based on advanced context
$mailerForUser = $container->make('Mailer', ['user' => 'John']);
$mailerForAdmin = $container->make('Mailer', ['user' => 'Admin']);
```

### 19. Resolving Method Injection
```php
use Virag\Container\Container;
use App\Services\PaymentService;

// Create a new container instance
$container = new Container();

// Binding a class to the container
$container->bind('PaymentService', PaymentService::class);

// Resolving method injection
$paymentService = $container->make('PaymentService');
$result = $paymentService->processPayment($container->make('PaymentGateway'));
```

### 20. Using Configuration Values
```php
use Virag\Container\Container;
use App\Services\MailService;

// Create a new container instance
$container = new Container();

// Binding a class to the container
$container->bind('MailService', MailService::class);

// Binding configuration values
$container->bindConfig('mail.host', 'smtp.example.com');
$container->bindConfig('mail.port', 587);

// Resolving the class with configuration values
$mailService = $container->make('MailService');
$mailService->setHost($container->getConfig('mail.host'));
$mailService->setPort($container->getConfig('mail.port'));
```

### 21. Using Tags for Multiple Resolving
```php
use Virag\Container\Container;
use App\Services\NotificationService;
use App\Services\EmailService;
use App\Services\SmsService;

// Create a new container instance
$container = new Container();

// Binding classes to the container
$container->bind('NotificationService', NotificationService::class);
$container->bind('EmailService', EmailService::class);
$container->bind('SmsService', SmsService::class);

// Tagging services
$container->tag('notification', ['NotificationService', 'EmailService', 'SmsService']);

// Resolving tagged services
$services = $container->getTagged('notification');
```

### 22. Using Lazy Loading
```php
use Virag\Container\Container;
use App\Services\CacheService;

// Create a new container instance
$container = new Container();

// Binding a class to the container
$container->bind('CacheService', CacheService::class);

// Resolving the class lazily
$cacheService = $container->factory('CacheService');
```

### 23. Using Custom Resolvers for Complex Dependencies
```php
use Virag\Container\Container;
use App\Services\ComplexService;

// Create a new container instance
$container = new Container();

// Registering a custom resolver
$container->registerCustomResolver('ComplexService', function($container) {
    $dependency1 = $container->make('Dependency1');
    $dependency2 = $container->make('Dependency2');
    return new ComplexService($dependency1, $dependency2);
});

// Resolving the class using custom resolver
$complexService = $container->make('ComplexService');
```

### 24. Using Delegate Container
```php
use Virag\Container\Container;
use Virag\Container\DelegateContainer;

// Create a new container instance
$container = new Container();

// Create a delegate container
$delegateContainer = new DelegateContainer();

// Bind a service to the delegate container
$delegateContainer->bind('Cache', RedisCache::class);

// Set the delegate container to the main container
$container->setDelegateContainer($delegateContainer);

// Resolving a service from the delegate container
$cacheService = $container->make('Cache');
```

### 25. Using Factory with Dynamic Parameters
```php
use Virag\Container\Container;
use App\Services\PaymentGateway;

// Create a new container instance
$container = new Container();

// Factory binding with dynamic parameters
$container->bindFactory('PaymentGateway', function($container, $currency) {
    return new PaymentGateway($currency, $container->make('Logger'));
});

// Resolving with factory and dynamic parameters
$paymentGatewayUSD = $container->factory('PaymentGateway', ['USD']);
$paymentGatewayEUR = $container->factory('PaymentGateway', ['EUR']);
```

### 26. Defining Inflectors for Interface Implementation
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Defining inflectors for interface implementation
$container->addInflector('PaymentProviderInterface', function($paymentProvider) use ($logger) {
    $paymentProvider->setLogger($logger);
});

// Resolving a service that implements PaymentProviderInterface
$paymentProvider = $container->make('StripePaymentProvider');
```

### 27. Using Shared Instances with Lazy Initialization
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Shared instance with lazy initialization
$container->share('CacheService', function() {
    return new CacheService();
});

// Resolving shared instance with lazy initialization
$cacheService = $container->getSharedInstance('CacheService');
```

### 28. Using Factories with Configuration
```php
use Virag\Container\Container;
use App\Services\MailService;

// Create a new container instance
$container = new Container();

// Factory binding with configuration parameters
$container->bindFactory('MailService', function($container, $config) {
    return new MailService($config['host'], $config['port']);
});

// Resolving with factory and configuration
$mailService = $container->factory('MailService', [
    'host' => $container->getConfig('mail.host'),
    'port' => $container->getConfig('mail.port')
]);
```

### 29. Resolving With Factories in Contextual Binding
```php
use Virag\Container\Container;
use App\Services\MailService;
use App\Services\SmsService;

// Create a new container instance
$container = new Container();

// Contextual binding with factories
$container->when('Notifier', function($container) {
    $container->needs('NotificationService', MailService::class)
        ->giveFactory(function() use ($container) {
            return $container->factory('MailService', [
                'host' => $container->getConfig('mail.host'),
                'port' => $container->getConfig('mail.port')
            ]);
        });

    $container->needs('NotificationService', SmsService::class)
        ->giveFactory(function() use ($container) {
            return $container->factory('SmsService', [
                'apiKey' => $container->getConfig('sms.api_key')
            ]);
        });
});

// Resolving based on contextual binding with factories
$notifier = $container->make('Notifier');
```

### 30. Using Closures as Resolvers
```php
use Virag\Container\Container;

// Create a new container instance
$container = new Container();

// Binding using closure as resolver
$container->bind('PaymentGateway', function($container) {
    return new PaymentGateway($container->make('Logger'));
});

// Resolving using closure as resolver
$paymentGateway = $container->make('PaymentGateway');
```

### 31. Using Tags with Contextual Binding
```php
use Virag\Container\Container;
use App\Services\NotificationService;
use App\Services\EmailService;
use App\Services\SmsService;

// Create a new container instance
$container = new Container();

// Tagging services
$container->tag('notification', ['EmailService', 'SmsService']);

// Contextual binding based on tags
$container->when('Notifier', function($container) {
    $container->needs('NotificationService')->giveTagged('notification');
});

// Resolving based on contextual binding with tags
$notifier = $container->make('Notifier');
```
### 32. using SetDelegateContainer

```php
use Virag\Container\Container;
use Virag\Container\DelegateContainer;

// Create a new container instance
$container = new Container();

// Create a delegate container instance
$delegateContainer = new DelegateContainer();

// Register some services in the delegate container
$delegateContainer->bind('Logger', FileLogger::class);
$delegateContainer->bind('Cache', RedisCache::class);

// Set the delegate container to the main container
$container->setDelegateContainer($delegateContainer);

// Now, if a service is not found in the main container,
// it will attempt to resolve it from the delegate container
$logger = $container->make('Logger'); // Resolves from delegate container
$cache = $container->make('Cache');   // Resolves from delegate container
```
### 33. Example Use of Service Provider

Here's an example of how you can use `setDelegateContainer()` in a service provider:

```php
use Virag\Container\Container;
use Virag\Container\DelegateContainer;

class MyServiceProvider
{
    public function register(Container $container)
    {
        // Register some services in the main container
        $container->bind('Logger', FileLogger::class);
        $container->bind('Cache', RedisCache::class);

        // Create a delegate container instance
        $delegateContainer = new DelegateContainer();

        // Register additional services in the delegate container
        $delegateContainer->bind('Mailer', SmtpMailer::class);
        $delegateContainer->bind('Database', MySqlConnection::class);

        // Set the delegate container to the main container
        $container->setDelegateContainer($delegateContainer);
    }
}
```

### 34. Register Multiple Services at once using service providers and set a delegate container

Here's an example demonstrating how you can register multiple services at once using service providers and set a delegate container:

```php
use Virag\Container\Container;
use App\Providers\AppServiceProvider;
use App\Providers\ViewServiceProvider;
use Virag\Container\ReflectionContainer;

// Create a new container instance
$container = new Container();

// Set delegate container for reflection-based resolution
$container->setDelegateContainer(new ReflectionContainer());

// Add multiple service providers to register services
$container->addServiceProvider(new AppServiceProvider());
$container->addServiceProvider(new ViewServiceProvider());

// Now, your container is configured with all the registered services from the service providers
```
