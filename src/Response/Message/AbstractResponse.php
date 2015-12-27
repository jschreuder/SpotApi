<?php declare(strict_types = 1);

namespace Spot\Api\Response\Message;

use Spot\Api\Message\AttributesArrayAccessTrait;
use Spot\Api\Request\Message\RequestInterface;

abstract class AbstractResponse implements ResponseInterface
{
    use AttributesArrayAccessTrait;

    /** @var  array */
    private $attributes;

    /** @var  string */
    private $contentType;

    public function __construct(array $attributes, RequestInterface $request)
    {
        $this->attributes = $attributes;
        $this->contentType = $request->getAcceptContentType();
    }

    public function getContentType() : string
    {
        return $this->contentType;
    }
}
