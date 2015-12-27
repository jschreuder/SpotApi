<?php

namespace spec\Spot\Api\Response\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Generator\SingleEntityGenerator;
use Spot\Api\Response\Message\ResponseInterface;

/** @mixin  SingleEntityGenerator */
class SingleEntityGeneratorSpec extends ObjectBehavior
{
    /** @var  \Tobscure\JsonApi\SerializerInterface */
    private $serializer;

    /** @var  callable */
    private $callable;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param  \Tobscure\JsonApi\SerializerInterface $serializer
     * @param  \Psr\Log\LoggerInterface $logger
     */
    public function let($serializer, $logger)
    {
        $this->serializer = $serializer;
        $this->callable = new class() {
            public $returnValue;
            public $exception;
            public function __invoke(ResponseInterface $response)
            {
                if ($this->exception) {
                    throw $this->exception;
                }
                return $this->returnValue;
            }
        };
        $this->logger = $logger;
        $this->beConstructedWith($this->serializer, $this->callable, $this->logger);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(SingleEntityGenerator::class);
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponse($response, $httpRequest)
    {
        $entity = (object) ['id' => 42, 'title' => 'life'];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entity);
        $response->offsetExists('includes')->willReturn(false);
        $this->callable->returnValue = [];
        $this->serializer->getType($entity)->willReturn('theAnswer');
        $this->serializer->getId($entity)->willReturn(42);
        $this->serializer->getAttributes($entity, null)->willReturn(['title' => 'life']);

        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiResponse::class);
        $body = $httpResponse->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"data":{"type":"theAnswer","id":"42","attributes":{"title":"life"}}}');
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     * @param  \Tobscure\JsonApi\Relationship $relation
     * @param  \Tobscure\JsonApi\ElementInterface $element
     */
    public function it_canGenerateAResponseWithInclude($response, $httpRequest, $relation, $element)
    {
        $entity = (object) ['id' => 42, 'title' => 'life'];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entity);
        $response->offsetExists('includes')->willReturn(true);
        $response->offsetGet('includes')->willReturn(['relation']);
        $this->callable->returnValue = [];
        $this->serializer->getType($entity)->willReturn('theAnswer');
        $this->serializer->getId($entity)->willReturn(42);
        $this->serializer->getAttributes($entity, null)->willReturn(['title' => 'life']);
        $this->serializer->getRelationship($entity, 'relation')->willReturn($relation);

        $relation->getData()->willReturn($element);
        $element->with([])->willReturn($element);
        $element->fields(null)->shouldBeCalled();
        $element->getResources()->willReturn([]);
        $relation->toArray()->willReturn([]);

        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiResponse::class);
        $body = $httpResponse->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"data":{"type":"theAnswer","id":"42","attributes":{"title":"life"},"relationships":{"relation":[]}}}');
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponseWithMetaData($response, $httpRequest)
    {
        $entity = (object) ['id' => 42, 'title' => 'life'];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entity);
        $response->offsetExists('includes')->willReturn(false);
        $this->callable->returnValue = ['offset' => 0];
        $this->serializer->getType($entity)->willReturn('theAnswer');
        $this->serializer->getId($entity)->willReturn(42);
        $this->serializer->getAttributes($entity, null)->willReturn(['title' => 'life']);

        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiResponse::class);
        $body = $httpResponse->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"data":{"type":"theAnswer","id":"42","attributes":{"title":"life"}},"meta":{"offset":0}}');
    }

    /**
     * @param  \Spot\Api\Response\Message\ResponseInterface $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_willErrorOnNonArrayResponse($response, $httpRequest)
    {
        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiErrorResponse::class);
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_willErrorOnEmptyDataResponse($response, $httpRequest)
    {
        $response->offsetExists('data')->willReturn(false);
        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiErrorResponse::class);
    }
}
