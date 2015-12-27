<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Pimple\Container;
use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\Response\Message\ResponseInterface;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\LoggableTrait;
use Zend\Diactoros\Response;

class GeneratorBus implements GeneratorInterface
{
    use LoggableTrait;

    /** @var  string[][] */
    private $generators = [];

    /** @var  Container */
    private $container;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function setGenerator(string $name, string $type, string $generator) : GeneratorBus
    {
        $this->generators[$name][$type] = $generator;
        return $this;
    }

    private function hasGenerator(string $name, string $contentType) : bool
    {
        if (empty($this->generators[$name])) {
            // No registered generators? Always false
            return false;
        } elseif ($contentType === '*/*') {
            // There are registered generators and requested is wildcard? Always true
            return true;
        }
        // Otherwise check if the specific contentType has a generator
        return isset($this->generators[$name][$contentType]);
    }

    private function getGenerator(string $name, string $contentType) : GeneratorInterface
    {
        // When wildcard and no specific wildcard handler was set: default to first generator
        if ($contentType === '*/*' && !isset($this->generators[$name][$contentType])) {
            return $this->container[reset($this->generators[$name])];
        }
        return $this->container[$this->generators[$name][$contentType]];
    }

    private function getGeneratorForResponse(ResponseInterface $response) : GeneratorInterface
    {
        $name = $response->getResponseName();
        foreach ($this->getRequestedContentTypes($response) as $contentType) {
            if ($this->hasGenerator($name, $contentType)) {
                return $this->getGenerator($name, $contentType);
            }
        }

        throw new \OutOfBoundsException(sprintf(
            'No generator registered for %s with content type: %s',
            $name,
            ($response->getContentType() ?? '(none)')
        ));
    }

    private function getRequestedContentTypes(ResponseInterface $response) : \SplPriorityQueue
    {
        preg_match_all(
            '#(?:^|(?:, ?))(?P<type>[^/,;]+/[^/,;]+)[^,]*?(?:;q=(?P<weight>[01]\.[0-9]+))?#uiD',
            $response->getContentType(),
            $matches
        );
        $types = new \SplPriorityQueue();
        foreach ($matches['type'] as $idx => $type) {
            $types->insert($type, $matches['weight'][$idx] ?: 1.0);
        }
        return $types;
    }

    /** {@inheritdoc} */
    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        try {
            $requestGenerator = $this->getGeneratorForResponse($response);
            $httpResponse = $requestGenerator->generateResponse($response);
        } catch (\Throwable $e) {
            $this->log(LogLevel::ERROR, 'Error during Response generation: ' . $e->getMessage());
            return new JsonApiErrorResponse([
                'title' => 'Server error: error during response generation',
                'status' => '500',
            ], 500);
        }

        return $httpResponse;
    }
}
