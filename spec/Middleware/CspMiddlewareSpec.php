<?php

namespace spec\Spot\Api\Middleware;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spot\Api\ApplicationInterface;
use Spot\Api\Middleware\CspMiddleware;

/** @mixin  CspMiddleware */
class CspMiddlewareSpec extends ObjectBehavior
{
    /** @var  ApplicationInterface */
    private $application;

    private $configFile = __DIR__ . '/CspMiddleware.json';

    public function let(ApplicationInterface $application)
    {
        $this->application = $application;
        $this->beConstructedWith($application, $this->configFile);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CspMiddleware::class);
    }

    public function it_can_execute_request(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->application->execute($request)->willReturn($response);
        $response->withAddedHeader('Content-Security-Policy', 'default-src \'self\'; ')->willReturn($response);
        $this->execute($request)->shouldReturn($response);
    }
}
