<?php declare(strict_types = 1);

namespace Spot\Api\ServiceProvider;

use Pimple\Container;

interface RepositoryProviderInterface
{
    public function registerRepositories(Container $container);
}
