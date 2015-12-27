<?php

namespace spec\Spot\Api\Response\Http;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spot\Api\Response\Http\JsonApiErrorResponse;

/** @mixin  JsonApiErrorResponse */
class JsonApiErrorResponseSpec extends ObjectBehavior
{
    /** @var  array */
    private $errors;

    /** @var  int */
    private $code;

    public function let()
    {
        $this->errors = [
            ['title' => 'Test message'],
        ];
        $this->code = 418;
        $this->beConstructedWith($this->errors, $this->code);
    }

    public function it_isInitializable()
    {
        $this->shouldHaveType(JsonApiErrorResponse::class);
    }

    public function it_getsJsonApiContentType()
    {
        $this->getHeaderLine('Content-Type')->shouldReturn('application/vnd.api+json');
    }

    public function it_getJsonApiBody()
    {
        $body = $this->getBody();
        $body->rewind();
        $body->getContents()->shouldReturn('{"errors":[{"title":"Test message"}]}');
        $this->getStatusCode()->shouldReturn(418);
    }
}
