<?php

namespace spec\Spot\Api\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Request\RequestException;

/** @mixin  RequestException */
class RequestExceptionSpec extends ObjectBehavior
{
    private $request;

    /**
     * @param  \Spot\Api\Request\RequestInterface $request
     */
    public function let($request)
    {
        $this->request = $request;
        $this->beConstructedWith('Reasons', $request);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RequestException::class);
        $this->shouldHaveType(\Exception::class);
    }

    public function it_comes_with_a_request_object()
    {
        $this->beConstructedWith('Reasons', $this->request);

        $this->getRequestObject()
            ->shouldReturn($this->request);
        $this->getMessage()
            ->shouldReturn('Reasons');
    }
}
