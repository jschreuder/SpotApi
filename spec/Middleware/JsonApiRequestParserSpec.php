<?php

namespace spec\Spot\Api\Middleware;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Middleware\JsonApiRequestParser;

/** @mixin  JsonApiRequestParser */
class JsonApiRequestParserSpec extends ObjectBehavior
{
    /** @var  \Spot\Api\ApplicationInterface */
    private $application;

    /**
     * @param  \Spot\Api\ApplicationInterface $application
     */
    public function let($application)
    {
        $this->application = $application;
        $this->beConstructedWith($application);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonApiRequestParser::class);
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $httpRequest1
     * @param  \Psr\Http\Message\ServerRequestInterface $httpRequest2
     * @param  \Psr\Http\Message\StreamInterface $body
     * @param  \Psr\Http\Message\ResponseInterface $httpResponse
     */
    public function it_can_execute($httpRequest1, $httpRequest2, $body, $httpResponse)
    {
        $array = ['body' => 'the answer to life, the universe and everything', 'status' => 42];
        $json = json_encode($array);

        $httpRequest1->getHeaderLine('Content-Type')->willReturn('application/vnd.api+json');
        $httpRequest1->getBody()->willReturn($body);
        $body->getSize()->willReturn(strlen($json));
        $body->getContents()->willReturn($json);

        $httpRequest1->withParsedBody($array)->willReturn($httpRequest2);
        $this->application->execute($httpRequest2)->willReturn($httpResponse);

        $this->execute($httpRequest1)->shouldReturn($httpResponse);
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $httpRequest
     * @param  \Psr\Http\Message\StreamInterface $body
     * @param  \Psr\Http\Message\ResponseInterface $httpResponse
     */
    public function it_wont_touch_non_json_api_requests($httpRequest, $body, $httpResponse)
    {
        $httpRequest->getHeaderLine('Content-Type')->willReturn('text/html');
        $this->application->execute($httpRequest)->willReturn($httpResponse);
        $this->execute($httpRequest)->shouldReturn($httpResponse);
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $httpRequest
     * @param  \Psr\Http\Message\StreamInterface $body
     * @param  \Psr\Http\Message\ResponseInterface $httpResponse
     */
    public function it_wont_touch_requests_with_empty_bodies($httpRequest, $body, $httpResponse)
    {
        $httpRequest->getHeaderLine('Content-Type')->willReturn('application/vnd.api+json');
        $httpRequest->getBody()->willReturn($body);
        $body->getSize()->willReturn(strlen(''));
        $this->application->execute($httpRequest)->willReturn($httpResponse);
        $this->execute($httpRequest)->shouldReturn($httpResponse);
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $httpRequest
     * @param  \Psr\Http\Message\StreamInterface $body
     */
    public function it_will_error_on_non_json_body($httpRequest, $body)
    {
        $notJson = 'i-am-not-JSON';
        $httpRequest->getHeaderLine('Content-Type')->willReturn('application/vnd.api+json');
        $httpRequest->getBody()->willReturn($body);
        $body->getSize()->willReturn(strlen($notJson));
        $body->getContents()->willReturn($notJson);
        $response = $this->execute($httpRequest);
        $response->shouldHaveType(JsonApiErrorResponse::class);
    }
}
