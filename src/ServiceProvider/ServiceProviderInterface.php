<?php

namespace ViragContainer\ServiceProvider;

use ViragContainer\Container\Container;

interface ServiceProviderInterface
{
    public function provides();
    public function boot(Container $container);
    public function register(Container $container);
    
}
