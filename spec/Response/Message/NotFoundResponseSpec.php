<?php

namespace spec\Spot\Api\Response\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Spot\Api\Message\AttributesArrayAccessSpecTrait;
use Spot\Api\Response\Message\NotFoundResponse;

require_once __DIR__ . '/../../Message/AttributesArrayAccessSpecTrait.php';

/** @mixin  \Spot\Api\Response\Message\NotFoundResponse */
class NotFoundResponseSpec extends ObjectBehavior
{
    use AttributesArrayAccessSpecTrait;

    private $name = 'error.notFound';
    private $contentType = 'application/vnd.api+json';
    private $request;

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function let($request)
    {
        $this->request = $request;
        $request->getAcceptContentType()->willReturn($this->contentType);
        $this->beConstructedWith([], $request);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(NotFoundResponse::class);
    }

    public function it_canGiveItsName()
    {
        $this->getResponseName()
            ->shouldReturn($this->name);
    }

    public function it_canGetItsContentType()
    {
        $this->getContentType()
            ->shouldReturn($this->contentType);
    }
}
