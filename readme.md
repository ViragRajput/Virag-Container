# Virag Container

Virag Container is a versatile and lightweight dependency injection container designed to streamline the management of class dependencies and service resolution within PHP applications. 

With Virag Container, you gain access to a comprehensive set of features essential for effective dependency management. This includes binding classes and interfaces to concrete implementations, resolving dependencies, defining singleton bindings, and managing contextual bindings. Additionally, advanced functionalities such as service providers, custom resolvers, inflectors, auto-wiring, and factories are seamlessly integrated, providing flexibility and extensibility.

Moreover, Virag Container is built with performance in mind, ensuring minimal overhead and optimal efficiency in your application. Whether you're building a small project or a large-scale application, Virag Container offers the tools you need to organize and maintain your codebase effectively.

## Installation

You can install Virag Container via Composer. Run the following command in your terminal:

```bash
composer require viragrajput/virag-container
```

## Usage

### Basic Usage

First, create an instance of the container:

```php
use Virag\Container\Container;

$container = new Container();
```

#### Binding Classes or Interfaces

You can bind classes or interfaces to concrete implementations using the `bind` method:

```php
$container->bind('LoggerInterface', 'FileLogger');
```

#### Resolving Dependencies

To resolve dependencies, use the `make` method:

```php
$logger = $container->make('LoggerInterface');
```

This will resolve the `FileLogger` instance.

#### Singleton Bindings

Singleton bindings can be defined using the `singleton` method:

```php
$container->singleton('DatabaseConnection', function () {
    return new DatabaseConnection();
});
```

#### Constructor Injection

The container supports constructor injection. For example:

```php
class UserRepository
{
    protected $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }
}

$container->bind('UserRepository', 'UserRepository');

$userRepository = $container->make('UserRepository');
```

#### Contextual Bindings

You can define contextual bindings using the `when` and `needs` methods:

```php
$container->when('PaymentGateway')
          ->needs('LoggerInterface', 'PaymentLogger');
```

### Advanced Usage

#### Service Providers

You can register service providers to bootstrap your application:

```php
$container->addServiceProvider('App\Providers\DatabaseServiceProvider');
```

#### Custom Resolvers

Define custom resolvers for specific bindings:

```php
$container->registerCustomResolver('MailService', function ($container) {
    return new ExternalMailService($container->make('Config')->get('mail.api_key'));
});
```

#### Inflectors

Inflectors allow you to modify instances after instantiation:

```php
$container->addInflector('LoggerAwareInterface', function ($loggerAware) use ($container) {
    $loggerAware->setLogger($container->make('Logger'));
});
```

#### Auto-Wiring

Enable auto-wiring to automatically resolve class dependencies:

```php
$container->enableAutoWiring();
```

#### Factories

Use factories for dynamic instance creation:

```php
$container->bindFactory('PaymentGateway', function ($container) {
    return new PaymentGateway($container->make('Config')->get('payment.gateway'));
});
```
## Examples: 
Explore various usage examples of the Virag Container Package in the "Docs" folder within this repository. These examples cover a wide range of scenarios to help you understand how to utilize the features of the package effectively.

## Contributing

We welcome contributions from the community! If you have any suggestions, find bugs, or want to add new features, please don't hesitate to submit issues or pull requests. Your contributions help improve the package for everyone. Let's build something great together!

## License

Virag Container is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
