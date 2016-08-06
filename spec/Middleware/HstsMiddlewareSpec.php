<?php

namespace spec\Spot\Api\Middleware;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Spot\Api\ApplicationInterface;
use Spot\Api\Middleware\HstsMiddleware;

/** @mixin HstsMiddleware */
class HstsMiddlewareSpec extends ObjectBehavior
{
    /** @var  ApplicationInterface */
    private $application;

    /** @var  int */
    private $maxAge = 31536000;

    /** @var  bool */
    private $includeSubDomains = true;

    /** @var  bool */
    private $preload = true;

    public function let(ApplicationInterface $application)
    {
        $this->application = $application;
        $this->beConstructedWith($application, $this->maxAge, $this->includeSubDomains, $this->preload);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(HstsMiddleware::class);
    }

    public function it_can_execute(ServerRequestInterface $request, UriInterface $uri, ResponseInterface $response)
    {
        $request->getUri()->willReturn($uri);
        $uri->getScheme()->willReturn('https');
        $this->application->execute($request)->willReturn($response);

        $response->withHeader('Strict-Transport-Security', 'max-age=' . $this->maxAge . '; includeSubDomains; preload')
            ->willReturn($response);
        $this->execute($request)->shouldReturn($response);
    }

    public function it_can_execute_without_extra_properties(
        ServerRequestInterface $request, UriInterface $uri, ResponseInterface $response
    )
    {
        $this->maxAge *= 2;
        $this->beConstructedWith($this->application, $this->maxAge, false, false);

        $request->getUri()->willReturn($uri);
        $uri->getScheme()->willReturn('https');
        $this->application->execute($request)->willReturn($response);

        $response->withHeader('Strict-Transport-Security', 'max-age=' . $this->maxAge)
            ->willReturn($response);
        $this->execute($request)->shouldReturn($response);
    }

    public function it_will_redirect_with_http_request(ServerRequestInterface $request, UriInterface $uri)
    {
        $request->getUri()->willReturn($uri);
        $uri->getScheme()->willReturn('http');
        $uri->withScheme('https')->willReturn($uri);
        $uri->withPort(443)->willReturn($uri);
        $uri->__toString()->willReturn($url = 'https://test.com');

        $response = $this->execute($request);
        $response->getHeaderLine('Location')->shouldReturn($url);
        $response->getStatusCode()->shouldReturn(307);
    }
}
