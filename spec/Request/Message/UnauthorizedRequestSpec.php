<?php

namespace spec\Spot\Api\Request\Message;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use spec\Spot\Api\Message\AttributesArrayAccessSpecTrait;
use Spot\Api\Request\Message\UnauthorizedRequest;

require_once __DIR__ . '/../../Message/AttributesArrayAccessSpecTrait.php';

/** @mixin  UnauthorizedRequest */
class UnauthorizedRequestSpec extends ObjectBehavior
{
    use AttributesArrayAccessSpecTrait;

    private $name = 'error.unauthorized';
    private $contentType = 'application/vnd.api+json';

    /** @var  RequestInterface */
    private $httpRequest;

    public function let(RequestInterface $httpRequest)
    {
        $this->httpRequest = $httpRequest;
        $httpRequest->getHeaderLine('Accept')->willReturn($this->contentType);
        $this->beConstructedWith([], $httpRequest);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(UnauthorizedRequest::class);
    }

    public function it_can_give_its_name()
    {
        $this->getRequestName()
            ->shouldReturn($this->name);
    }

    public function it_can_get_its_content_type()
    {
        $this->getAcceptContentType()
            ->shouldReturn($this->contentType);
    }
}
