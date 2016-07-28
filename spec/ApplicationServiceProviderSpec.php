<?php

namespace spec\Spot\Api;

use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\ApplicationServiceProvider;

/** @mixin  ApplicationServiceProvider */
class ApplicationServiceProviderSpec extends ObjectBehavior
{
    /** @var  \Pimple\Container */
    private $container;

    /** @var  \Spot\Api\Request\HttpRequestParser\HttpRequestParserBus */
    private $router;

    /** @var  \FastRoute\RouteCollector */
    private $routeCollector;

    /** @var  \Spot\Api\Request\Executor\ExecutorBus */
    private $executorBus;

    /** @var  \Spot\Api\Response\Generator\GeneratorBus */
    private $generatorBus;

    /**
     * @param  \Pimple\Container $container
     * @param  \Spot\Api\Request\HttpRequestParser\HttpRequestParserBus $router
     * @param  \FastRoute\RouteCollector $routeCollector
     * @param  \Spot\Api\Request\Executor\ExecutorBus $executorBus
     * @param  \Spot\Api\Response\Generator\GeneratorBus $generatorBus
     */
    public function let($container, $router, $routeCollector, $executorBus, $generatorBus)
    {
        $this->container = $container;
        $this->router = $router;
        $this->routeCollector = $routeCollector;
        $this->executorBus = $executorBus;
        $this->generatorBus = $generatorBus;

        $this->beConstructedWith($container, $router, $routeCollector, $executorBus, $generatorBus);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationServiceProvider::class);
    }

    /**
     * @param  \Pimple\ServiceProviderInterface $serviceModule
     * @param  \Spot\Api\ServiceProvider\RoutingProviderInterface $routeModule
     * @param  \Spot\Api\ServiceProvider\RepositoryProviderInterface $repoModule
     */
    public function it_can_add_modules($serviceModule, $routeModule, $repoModule)
    {
        $serviceModule->register($this->container)
            ->shouldBeCalled();
        $routeModule->registerRouting($this->container, $this)
            ->shouldBeCalled();
        $repoModule->registerRepositories($this->container)
            ->shouldBeCalled();

        $this->addModules([$serviceModule, $routeModule, $repoModule]);
    }

    public function it_can_add_a_request_parser()
    {
        $method = 'GET';
        $path = '/some/way';
        $parser = 'my.way';
        $this->routeCollector->addRoute($method, $path, $parser)
            ->shouldBeCalled();
        $this->addParser($method, $path, $parser)
            ->shouldReturn($this);
    }

    public function it_can_add_an_executor()
    {
        $name = 'my.way';
        $executor = 'i.did.it';
        $this->executorBus->setExecutor($name, $executor)
            ->shouldBeCalled();
        $this->addExecutor($name, $executor)
            ->shouldReturn($this);
    }

    public function it_can_add_an_generator()
    {
        $name = 'my.way';
        $type = 'application/song';
        $generator = 'i.did.it';
        $this->generatorBus->setGenerator($name, $type, $generator)
            ->shouldBeCalled();
        $this->addGenerator($name, $type, $generator)
            ->shouldReturn($this);
    }

    public function it_can_return_the_HttpRequestParserBus()
    {
        $this->router->setRouter(new Argument\Token\TypeToken(GroupCountBasedDispatcher::class))
            ->willReturn($this->router);

        $this->getHttpRequestParser()->shouldReturn($this->router);
    }

    public function it_can_return_the_ExecutorBus()
    {
        $this->getExecutor()->shouldReturn($this->executorBus);
    }

    public function it_can_return_the_GeneratorBus()
    {
        $this->getGenerator()->shouldReturn($this->generatorBus);
    }

    /**
     * @param   \Pimple\Container $container
     */
    public function it_can_register_the_app($container)
    {
        $container->offsetSet('app.httpRequestParser', new Argument\Token\TypeToken(\Closure::class))
            ->shouldBeCalled();
        $container->offsetSet('app.executor', new Argument\Token\TypeToken(\Closure::class))
            ->shouldBeCalled();
        $container->offsetSet('app.generator', new Argument\Token\TypeToken(\Closure::class))
            ->shouldBeCalled();
        $container->offsetSet('app', new Argument\Token\TypeToken(\Closure::class))
            ->shouldBeCalled();
        $this->register($container);
    }
}
