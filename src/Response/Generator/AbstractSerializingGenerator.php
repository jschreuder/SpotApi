<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Spot\Api\LoggableTrait;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\ElementInterface;
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

    abstract protected function validResponse(ResponseInterface $response) : bool;

    abstract protected function generateData(ResponseInterface $response) : ElementInterface;

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

    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        if (!$this->validResponse($response)) {
            $this->log(LogLevel::ERROR, 'No data present in Response.');
            return new JsonApiErrorResponse([
                'title' => 'Server Error: no data to generate response from',
                'status' => '500',
            ], 500);
        }

        $document = new Document($this->generateData($response));
        $this->generateMetaData($response, $document);

        return new JsonApiResponse($document);
    }
}
