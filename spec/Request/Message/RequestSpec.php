<?php

namespace spec\Spot\Api\Request\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Spot\Api\Message\AttributesArrayAccessSpecTrait;
use Spot\Api\Request\Message\Request;

require_once __DIR__ . '/../../Message/AttributesArrayAccessSpecTrait.php';

/** @mixin  \Spot\Api\Request\Message\Request */
class RequestSpec extends ObjectBehavior
{
    use AttributesArrayAccessSpecTrait;

    private $name = 'array.request';
    private $contentType = 'application/vnd.api+json';
    private $data = ['answer' => 42];

    /** @var  \Psr\Http\Message\RequestInterface */
    private $httpRequest;

    /**
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function let($httpRequest)
    {
        $this->httpRequest = $httpRequest;
        $httpRequest->getHeaderLine('Accept')->willReturn($this->contentType);
        $this->beConstructedWith($this->name, $this->data, $httpRequest);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(Request::class);
    }

    public function it_canGiveItsName()
    {
        $this->getRequestName()
            ->shouldReturn($this->name);
    }

    public function it_canGiveItsData()
    {
        $this->getAttributes()
            ->shouldReturn($this->data);
    }

    public function it_canGetItsContentType()
    {
        $this->getAcceptContentType()
            ->shouldReturn($this->contentType);
    }
}