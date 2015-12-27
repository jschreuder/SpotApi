<?php

namespace spec\Spot\Api\Response;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Message\Response;
use Spot\Api\Response\ResponseException;

/** @mixin  ResponseException */
class ResponseExceptionSpec extends ObjectBehavior
{
    private $response;

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function let($request)
    {
        $request->getAcceptContentType()->willReturn('application/vnd.api+json');
        $this->response = new Response('destroy.earth', ['answer' => 'misfiled'], $request->getWrappedObject());
        $this->beConstructedWith('Reasons', $this->response);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(ResponseException::class);
    }

    public function it_comesWithAResponseObject()
    {
        $this->getResponseObject()
            ->shouldReturn($this->response);
        $this->getMessage()
            ->shouldReturn('Reasons');
    }
}
