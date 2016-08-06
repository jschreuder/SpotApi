<?php declare(strict_types = 1);

namespace Spot\Api\Middleware;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\ApplicationInterface;
use Spot\Api\LoggableTrait;
use Spot\Api\Response\Http\JsonApiErrorResponse;

class ExceptionCatchingMiddleware implements ApplicationInterface
{
    use LoggableTrait;

    /** @var  ApplicationInterface */
    private $application;

    public function __construct(ApplicationInterface $application, LoggerInterface $logger)
    {
        $this->application = $application;
        $this->logger = $logger;
    }

    public function execute(ServerHttpRequest $httpRequest) : HttpResponse
    {
        try {
            return $this->application->execute($httpRequest);
        } catch (\Throwable $exception) {
            $this->log(LogLevel::CRITICAL, $exception->getMessage());
            return new JsonApiErrorResponse([
                'title' => 'Fatal error'
            ], 500);
        }
    }
}
