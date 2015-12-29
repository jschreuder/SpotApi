<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\LoggableTrait;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Document;
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

    /** @return  void */
    protected function generateMetaData(ResponseInterface $response, Document $document)
    {
        if (is_null($this->metaDataGenerator)) {
            return;
        }

        $metaData = call_user_func($this->metaDataGenerator, $response);
        foreach ($metaData as $key => $value) {
            $document->addMeta($key, $value);
        }
    }

    protected function getSerializer() : SerializerInterface
    {
        return $this->serializer;
    }

    protected function noDataResponse() : JsonApiErrorResponse
    {
        $this->log(LogLevel::ERROR, 'No data present in Response.');
        return new JsonApiErrorResponse([
            'title' => 'Server Error: no data to generate response from',
            'status' => '500',
        ], 500);
    }
}
