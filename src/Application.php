<?php declare(strict_types = 1);

namespace Spot\Api;

use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Request\HttpRequestParser\HttpRequestParserInterface;
use Spot\Api\Request\RequestException;
use Spot\Api\Response\Generator\GeneratorInterface;
use Spot\Api\Response\ResponseException;

class Application implements ApplicationInterface
{
    use LoggableTrait;

    /** @var  HttpRequestParserInterface */
    private $requestParser;

    /** @var  ExecutorInterface */
    private $executor;

    /** @var  GeneratorInterface */
    private $generator;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(
        HttpRequestParserInterface $requestParser,
        ExecutorInterface $executor,
        GeneratorInterface $generator,
        LoggerInterface $logger
    ) {
        $this->requestParser = $requestParser;
        $this->executor = $executor;
        $this->generator = $generator;
        $this->logger = $logger;
    }

    /** {@inheritdoc} */
    public function execute(ServerHttpRequest $httpRequest) : HttpResponse
    {
        $this->log(LogLevel::INFO, 'Starting execution.');
        try {
            $requestMessage = $this->requestParser->parseHttpRequest($httpRequest, []);
            $this->log(LogLevel::INFO, 'Successfully parsed HTTP request into Request message.');
        } catch (RequestException $requestException) {
            $this->log(LogLevel::ERROR, 'Request parsing ended in exception: ' . $requestException->getMessage());
            $requestMessage = $requestException->getRequestObject();
        }

        try {
            $responseMessage = $this->executor->executeRequest($requestMessage);
        } catch (ResponseException $responseException) {
            $this->log(LogLevel::ERROR, 'Request execution ended in exception: ' . $responseException->getMessage());
            $responseMessage = $responseException->getResponseObject();
        }

        $httpResponse = $this->generator->generateResponse($responseMessage);
        $this->log(LogLevel::INFO, 'Successfully generated HTTP response.');

        return $httpResponse;
    }
}
