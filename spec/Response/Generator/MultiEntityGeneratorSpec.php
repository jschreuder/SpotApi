<?php

namespace spec\Spot\Api\Response\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Generator\MultiEntityGenerator;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;

class MultiEntityGeneratorSpec extends ObjectBehavior
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
        $this->shouldHaveType(MultiEntityGenerator::class);
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponse($response, $httpRequest)
    {
        $entity1 = (object) ['id' => 42, 'title' => 'life'];
        $entity2 = (object) ['id' => 1138, 'title' => 'thx'];
        $entities = [$entity1, $entity2];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entities);
        $response->offsetExists('includes')->willReturn(false);
        $this->callable->returnValue = [];
        $this->serializer->getType($entity1)->willReturn('theAnswer');
        $this->serializer->getType($entity2)->willReturn('theAnswer');
        $this->serializer->getId($entity1)->willReturn(42);
        $this->serializer->getId($entity2)->willReturn(1138);
        $this->serializer->getAttributes($entity1, null)->willReturn(['title' => 'life']);
        $this->serializer->getAttributes($entity2, null)->willReturn(['title' => 'thx']);

        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiResponse::class);
        $body = $httpResponse->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"data":[{"type":"theAnswer","id":"42","attributes":{"title":"life"}},{"type":"theAnswer","id":"1138","attributes":{"title":"thx"}}]}');
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
        $entities = [$entity];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entities);
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
        $body->getContents()->shouldReturn('{"data":[{"type":"theAnswer","id":"42","attributes":{"title":"life"},"relationships":{"relation":[]}}]}');
    }

    /**
     * @param  \Spot\Api\Response\Message\Response $response
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function it_canGenerateAResponseWithMetaData($response, $httpRequest)
    {
        $entities = [];
        $response->offsetExists('data')->willReturn(true);
        $response->offsetGet('data')->willReturn($entities);
        $response->offsetExists('includes')->willReturn(false);
        $this->callable->returnValue = ['offset' => 0];

        $httpResponse = $this->generateResponse($response, $httpRequest);
        $httpResponse->shouldHaveType(JsonApiResponse::class);
        $body = $httpResponse->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"data":[],"meta":{"offset":0}}');
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
