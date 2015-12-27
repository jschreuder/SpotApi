<?php declare(strict_types = 1);

namespace Spot\Api\Request\Executor;

use Pimple\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\Request\Message\RequestInterface;
use Spot\Api\Response\Message\NotFoundResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Spot\Api\Response\ResponseException;
use Spot\Api\LoggableTrait;

class ExecutorBus implements ExecutorInterface
{
    use LoggableTrait;

    /** @var  string[] */
    private $executors = [];

    /** @var  Container */
    private $container;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function setExecutor(string $name, string $executor) : ExecutorBus
    {
        $this->executors[$name] = $executor;
        return $this;
    }

    protected function getExecutor(RequestInterface $request) : ExecutorInterface
    {
        $executor = $this->container[$this->executors[$request->getRequestName()]];
        if (!$executor instanceof ExecutorInterface) {
            throw new \RuntimeException('Executor must implement ExecutorInterface.');
        }
        return $executor;
    }

    protected function supports(RequestInterface $request) : bool
    {
        return array_key_exists($request->getRequestName(), $this->executors)
            && isset($this->container[$this->executors[$request->getRequestName()]]);
    }

    /** {@inheritdoc} */
    public function executeRequest(RequestInterface $requestMessage) : ResponseInterface
    {
        if (!$this->supports($requestMessage)) {
            $msg = 'Unsupported request: ' . $requestMessage->getRequestName();
            $this->log(LogLevel::WARNING, $msg);
            throw new ResponseException($msg, new NotFoundResponse([], $requestMessage));
        }

        $requestExecutor = $this->getExecutor($requestMessage);
        $responseMessage = $requestExecutor->executeRequest($requestMessage);

        return $responseMessage;
    }
}
