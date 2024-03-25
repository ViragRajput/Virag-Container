<?php

/*
 * This file is part of the ViragContainer package.
 *
 * ViragContainer is a lightweight PHP dependency injection container designed to manage object 
 * creation and resolution.
 * It provides a flexible and powerful way to manage dependencies in your PHP projects.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Virag\Container\ServiceProvider;

use Virag\Container\Container;

interface ServiceProviderInterface
{
    public function register(Container $container);
    
}
