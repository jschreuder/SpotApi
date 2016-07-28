<?php declare(strict_types = 1);

namespace Spot\Api;

use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteCollector;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Spot\Api\Request\HttpRequestParser\HttpRequestParserInterface;
use Spot\Api\Request\HttpRequestParser\HttpRequestParserBus;
use Spot\Api\Request\Executor\ExecutorBus;
use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Response\Generator\GeneratorBus;
use Spot\Api\Response\Generator\GeneratorInterface;
use Spot\Api\ServiceProvider\RepositoryProviderInterface;
use Spot\Api\ServiceProvider\RoutingProviderInterface;

class ApplicationServiceProvider implements ServiceProviderInterface
{
    /** @var  Container */
    private $container;

    /** @var  HttpRequestParserBus */
    private $router;

    /** @var  RouteCollector */
    private $routeCollector;

    /** @var  ExecutorBus */
    private $executorBus;

    /** @var  GeneratorBus */
    private $generatorBus;

    /** @var  \ReflectionMethod[] */
    private $moduleRegistrars;

    public function __construct(
        Container $container,
        HttpRequestParserBus $router,
        RouteCollector $routeCollector,
        ExecutorBus $executorBus,
        GeneratorBus $generatorBus
    ) {
        $this->container = $container;
        $this->router = $router;
        $this->routeCollector = $routeCollector;
        $this->executorBus = $executorBus;
        $this->generatorBus = $generatorBus;

        $this->setupRegistrars();
    }

    private function setupRegistrars()
    {
        $this->moduleRegistrars = [
            new \ReflectionMethod($this, 'registerServices'),
            new \ReflectionMethod($this, 'registerRouting'),
            new \ReflectionMethod($this, 'registerRepositories'),
        ];
    }

    public function addModule($module) : ApplicationServiceProvider
    {
        foreach ($this->moduleRegistrars as $moduleRegistrar) {
            if ($moduleRegistrar->getParameters()[0]->getClass()->isInstance($module)) {
                $moduleRegistrar->invoke($this, $module);
            }
        }
        return $this;
    }

    public function addModules(array $modules) : ApplicationServiceProvider
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }
        return $this;
    }

    public function registerServices(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register($this->container);
    }

    public function registerRouting(RoutingProviderInterface $routingProvider)
    {
        $routingProvider->registerRouting($this->container, $this);
    }

    public function registerRepositories(RepositoryProviderInterface $repositoryProvider)
    {
        $repositoryProvider->registerRepositories($this->container);
    }

    public function addParser(string $method, string $path, string $httpRequestParser) : ApplicationServiceProvider
    {
        $this->routeCollector->addRoute($method, $path, $httpRequestParser);
        return $this;
    }

    public function addExecutor(string $requestName, string $executor) : ApplicationServiceProvider
    {
        $this->executorBus->setExecutor($requestName, $executor);
        return $this;
    }

    public function addGenerator(string $responseName, string $contentType, string $generator) : ApplicationServiceProvider
    {
        $this->generatorBus->setGenerator($responseName, $contentType, $generator);
        return $this;
    }

    /** {@inheritdoc} */
    public function getHttpRequestParser() : HttpRequestParserInterface
    {
        return $this->router->setRouter(new GroupCountBasedDispatcher($this->routeCollector->getData()));
    }

    /** {@inheritdoc} */
    public function getExecutor() : ExecutorInterface
    {
        return $this->executorBus;
    }

    /** {@inheritdoc} */
    public function getGenerator() : GeneratorInterface
    {
        return $this->generatorBus;
    }

    /** {@inheritdoc} */
    public function register(Container $container)
    {
        $container['app.httpRequestParser'] = function () {
            return $this->getHttpRequestParser();
        };
        $container['app.executor'] = function () {
            return $this->getExecutor();
        };
        $container['app.generator'] = function () {
            return $this->getGenerator();
        };

        $container['app'] = function(Container $container) {
            return new Application(
                $container['app.httpRequestParser'],
                $container['app.executor'],
                $container['app.generator'],
                $container['logger']
            );
        };
    }
}
