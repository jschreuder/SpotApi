<?php declare(strict_types = 1);

namespace Spot\Api\ServiceProvider;

use Pimple\Container;
use Spot\Api\ApplicationServiceProvider;

interface RoutingProviderInterface
{
    const JSON_API_CT = 'application/vnd.api+json';

    public function registerRouting(Container $container, ApplicationServiceProvider $builder);
}
