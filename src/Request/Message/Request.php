<?php declare(strict_types = 1);

namespace Spot\Api\Request\Message;

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Spot\Api\Message\AttributesArrayAccessTrait;

class Request implements RequestInterface
{
    use AttributesArrayAccessTrait;

    /** @var  string */
    private $name;

    /** @var  array */
    private $attributes;

    /** @var  string */
    private $acceptContentType;

    public function __construct(string $name, array $attributes, HttpRequestInterface $httpRequest)
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->acceptContentType = $httpRequest->getHeaderLine('Accept');
    }

    /** {@inheritdoc} */
    public function getRequestName() : string
    {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function getAcceptContentType() : string
    {
        return $this->acceptContentType;
    }
}
