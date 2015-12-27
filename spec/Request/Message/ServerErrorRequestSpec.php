<?php

namespace spec\Spot\Api\Request\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Spot\Api\Message\AttributesArrayAccessSpecTrait;
use Spot\Api\Request\Message\ServerErrorRequest;

require_once __DIR__ . '/../../Message/AttributesArrayAccessSpecTrait.php';

/** @mixin  \Spot\Api\Request\Message\ServerErrorRequest */
class ServerErrorRequestSpec extends ObjectBehavior
{
    use AttributesArrayAccessSpecTrait;

    private $name = 'error.serverError';
    private $contentType = 'application/vnd.api+json';

    /** @var  \Psr\Http\Message\RequestInterface */
    private $httpRequest;

    /**
     * @param  \Psr\Http\Message\RequestInterface $httpRequest
     */
    public function let($httpRequest)
    {
        $this->httpRequest = $httpRequest;
        $httpRequest->getHeaderLine('Accept')->willReturn($this->contentType);
        $this->beConstructedWith([], $httpRequest);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(ServerErrorRequest::class);
    }

    public function it_canGiveItsName()
    {
        $this->getRequestName()
            ->shouldReturn($this->name);
    }

    public function it_canGetItsContentType()
    {
        $this->getAcceptContentType()
            ->shouldReturn($this->contentType);
    }
}
