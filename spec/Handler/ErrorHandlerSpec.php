<?php

namespace spec\Spot\Api\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Handler\ErrorHandler;
use Spot\Api\Response\Message\Response;

/** @mixin  ErrorHandler */
class ErrorHandlerSpec extends ObjectBehavior
{
    /** @var  string */
    private $name = 'test.nest';

    /** @var  int */
    private $statusCode = 418;

    /** @var  string */
    private $message = 'Test a nest on a vest to rest.';

    public function let()
    {
        $this->beConstructedWith($this->name, $this->statusCode, $this->message);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(\Spot\Api\Handler\ErrorHandler::class);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canExecuteARequest($request)
    {
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');
        $request->offsetExists('errors')->willReturn(false);
        $response = $this->executeRequest($request);
        $response->shouldHaveType(Response::class);
        $response->getResponseName()->shouldReturn($this->name);
        $response->getAttributes()->shouldReturn([]);
    }

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canPassOnErrorsToTheResponse($request)
    {
        $errors = ['error1' => 'your first mistake', 'error2' => 'was trying to run this mess'];
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');
        $request->offsetExists('errors')->willReturn(true);
        $request->offsetGet('errors')->willReturn($errors);
        $response = $this->executeRequest($request);
        $response->shouldHaveType(Response::class);
        $response->getResponseName()->shouldReturn($this->name);
        $response->getAttributes()->shouldReturn(['errors' => $errors]);
    }

    /**
     * @param  \Spot\Api\Response\Message\ResponseInterface $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponse($response)
    {
        $response->offsetExists('errors')->willReturn(false);
        $httpResponse = $this->generateResponse($response);
        $httpResponse->shouldHaveType(JsonApiErrorResponse::class);

        $body = $httpResponse->getBody();
        $body->getContents()->shouldReturn('{"errors":[{"title":"Test a nest on a vest to rest."}]}');
    }

    /**
     * @param  \Spot\Api\Response\Message\ResponseInterface $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponseWithGivenErrors($response)
    {
        $response->offsetExists('errors')->willReturn(true);
        $response->offsetGet('errors')->willReturn([
            ['title' => 'One'],
            ['title' => 'Two'],
        ]);

        $httpResponse = $this->generateResponse($response);
        $httpResponse->shouldHaveType(JsonApiErrorResponse::class);

        $body = $httpResponse->getBody();
        $body->getContents()->shouldReturn('{"errors":[{"title":"One"},{"title":"Two"}]}');
    }
}
