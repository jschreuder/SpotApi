<?php

namespace spec\Spot\Api\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Request\Message\Request;
use Spot\Api\Request\RequestException;

/** @mixin  RequestException */
class RequestExceptionSpec extends ObjectBehavior
{
    private $request;

    /**
     * @param  \Spot\Api\Request\Message\RequestInterface $request
     */
    public function let($request)
    {
        $this->request = $request;
        $this->beConstructedWith('Reasons', $request);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(RequestException::class);
        $this->shouldHaveType(\Exception::class);
    }

    public function it_comesWithARequestObject()
    {
        $this->beConstructedWith('Reasons', $this->request);

        $this->getRequestObject()
            ->shouldReturn($this->request);
        $this->getMessage()
            ->shouldReturn('Reasons');
    }
}
