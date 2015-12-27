<?php declare(strict_types = 1);

namespace Spot\Api\Response\Message;

use Spot\Api\Message\AttributesArrayAccessTrait;
use Spot\Api\Request\Message\RequestInterface;

class Response implements ResponseInterface
{
    use AttributesArrayAccessTrait;

    /** @var  string */
    private $name;

    /** @var  array */
    private $attributes;

    /** @var  string */
    private $contentType;

    public function __construct(string $name, array $data, RequestInterface $request)
    {
        $this->name = $name;
        $this->attributes = $data;
        $this->contentType = $request->getAcceptContentType();
    }

    /** {@inheritdoc} */
    public function getResponseName() : string
    {
        return $this->name;
    }

    public function getContentType() : string
    {
        return $this->contentType;
    }
}
