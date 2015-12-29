<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\LoggableTrait;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractSerializingGenerator implements GeneratorInterface
{
    use LoggableTrait;

    /** @var  SerializerInterface */
    private $serializer;

    /** @var  callable */
    private $metaDataGenerator;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(
        SerializerInterface $serializer,
        callable $metaDataGenerator = null,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->metaDataGenerator = $metaDataGenerator;
        $this->logger = $logger;
    }

    protected function metaDataGenerator(ResponseInterface $response) : array
    {
        return $this->metaDataGenerator ? call_user_func($this->metaDataGenerator, $response) : [];
    }

    protected function getSerializer() : SerializerInterface
    {
        return $this->serializer;
    }
}
