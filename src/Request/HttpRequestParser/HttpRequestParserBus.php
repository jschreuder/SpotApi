<?php declare(strict_types = 1);

namespace Spot\Api\Request\HttpRequestParser;

use FastRoute\Dispatcher as Router;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\Request\Message\NotFoundRequest;
use Spot\Api\Request\Message\RequestInterface;
use Spot\Api\Request\Message\ServerErrorRequest;
use Spot\Api\LoggableTrait;
use Spot\Api\Request\RequestException;

class HttpRequestParserBus implements HttpRequestParserInterface
{
    use LoggableTrait;

    /** @var  Container */
    private $container;

    /** @var  Router */
    private $router;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function setRouter(Router $router) : HttpRequestParserBus
    {
        $this->router = $router;
        return $this;
    }

    private function getRouter() : Router
    {
        if (is_null($this->router)) {
            throw new \RuntimeException('Router must be provided to allow Request parsing.');
        }
        return $this->router;
    }

    private function getHttpRequestParser(string $name) : HttpRequestParserInterface
    {
        return $this->container[$name];
    }

    /** {@inheritdoc} */
    public function parseHttpRequest(ServerHttpRequest $httpRequest, array $attributes) : RequestInterface
    {
        try {
            $routeInfo = $this->getRouter()->dispatch(
                $httpRequest->getMethod(),
                $httpRequest->getUri()->getPath()
            );
            $request = $this->getRequest($routeInfo, $httpRequest, $attributes);
        } catch (RequestException $exception) {
            $request = $exception->getRequestObject();
        } catch (\Throwable $exception) {
            $this->log(LogLevel::ERROR, $exception->getMessage());
            $request = new ServerErrorRequest([], $httpRequest);
        }

        return $request;
    }

    private function getRequest(array $routeInfo, ServerHttpRequest $httpRequest, array $attributes) : RequestInterface
    {
        switch ($routeInfo[0]) {
            case Router::NOT_FOUND:
            case Router::METHOD_NOT_ALLOWED:
                $this->log(LogLevel::INFO, 'No route found: ' . $httpRequest->getUri()->getPath());
                $request = new NotFoundRequest([], $httpRequest);
                break;
            case Router::FOUND:
                $parser = $this->getHttpRequestParser($routeInfo[1]);
                $request = $parser->parseHttpRequest($httpRequest, array_merge($attributes, $routeInfo[2]));
                $this->log(LogLevel::INFO, 'Route found: ' . $routeInfo[1]);
                break;
            default:
                throw new RequestException('Routing error', new ServerErrorRequest([], $httpRequest));
        }
        return $request;
    }
}
