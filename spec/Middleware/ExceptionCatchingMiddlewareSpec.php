<?php

namespace spec\Spot\Api\Middleware;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\ApplicationInterface;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Middleware\ExceptionCatchingMiddleware;

/** @mixin  ExceptionCatchingMiddleware */
class ExceptionCatchingMiddlewareSpec extends ObjectBehavior
{
    /** @var  ApplicationInterface */
    private $application;

    /** @var  LoggerInterface */
    private $logger;

    public function let(ApplicationInterface $application, LoggerInterface $logger)
    {
        $this->application = $application;
        $this->logger = $logger;
        $this->beConstructedWith($application, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ExceptionCatchingMiddleware::class);
    }

    public function it_can_execute_a_request(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->application->execute($request)->willReturn($response);
        $this->execute($request)->shouldReturn($response);
    }

    public function it_will_handle_all_exceptions_and_log_the_error(ServerRequestInterface $request)
    {
        $message = sha1(uniqid());
        $this->application->execute($request)->willThrow(new \RuntimeException($message));
        $this->logger->log(
            LogLevel::CRITICAL,
            '[' . ExceptionCatchingMiddleware::class . '] ' . $message,
            []
        )->shouldBeCalled();

        $response = $this->execute($request);
        $response->shouldHaveType(JsonApiErrorResponse::class);
    }
}
