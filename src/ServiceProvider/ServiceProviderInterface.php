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

namespace ViragContainer\ServiceProvider;

use ViragContainer\Container\Container;

interface ServiceProviderInterface
{
    public function register(Container $container);
    
}
