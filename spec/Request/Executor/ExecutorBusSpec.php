<?php

namespace spec\Spot\Api\Request\Executor;

use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface as HttpRequest;
use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Request\Executor\ExecutorBus;
use Spot\Api\Request\Message\RequestInterface;
use Spot\Api\Response\Message\Response;
use Spot\Api\Response\Message\ResponseInterface;
use Spot\Api\Response\ResponseException;

/** @mixin  \Spot\Api\Request\Executor\ExecutorBus */
class ExecutorBusSpec extends ObjectBehavior
{
    /** @var  Container */
    private $container;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param   \Psr\Log\LoggerInterface $logger
     */
    public function let($logger)
    {
        $this->container = new Container();
        $this->logger = $logger;
        $this->beConstructedWith($this->container, $logger);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(\Spot\Api\Request\Executor\ExecutorBus::class);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function it_canExecuteSuccessfully($request)
    {
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');
        $requestName = 'request.name';
        $response = new Response($requestName, [], $request->getWrappedObject());
        $executorName = 'executor.test';
        $executor = new class($response) implements ExecutorInterface {
            private $response;
            public function __construct($response)
            {
                $this->response = $response;
            }
            public function executeRequest(RequestInterface $request) : ResponseInterface
            {
                return $this->response;
            }
        };
        $this->container[$executorName] = $executor;
        $this->setExecutor($requestName, $executorName)
            ->shouldReturn($this);

        $request->getRequestName()
            ->willReturn($requestName);

        $this->executeRequest($request)
            ->shouldReturn($response);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function it_willErrorOnUnsupportedRequest($request)
    {
        $requestName = 'request.name';
        $request->getRequestName()
            ->willReturn($requestName);
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');

        $this->shouldThrow(ResponseException::class)->duringExecuteRequest($request);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function it_willErrorOnUndefinedExecutor($request)
    {
        $requestName = 'request.name';
        $executorName = 'executor.test';

        $this->setExecutor($requestName, $executorName)
            ->shouldReturn($this);

        $request->getRequestName()
            ->willReturn($requestName);
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');

        $this->shouldThrow(ResponseException::class)->duringExecuteRequest($request);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function it_willErrorOnInvalidExecutor($request)
    {
        $requestName = 'request.name';
        $executorName = 'executor.test';

        $this->setExecutor($requestName, $executorName)
            ->shouldReturn($this);
        $this->container[$executorName] = new \stdClass();

        $request->getRequestName()
            ->willReturn($requestName);
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');

        $this->shouldThrow(\RuntimeException::class)->duringExecuteRequest($request);
    }
}
