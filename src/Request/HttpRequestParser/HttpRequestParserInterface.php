<?php declare(strict_types = 1);

namespace Spot\Api\Request\HttpRequestParser;

use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Spot\Api\Request\Message\RequestInterface;

interface HttpRequestParserInterface
{
    /**
     * MUST catch all exceptions internally and throw only RequestException
     * instances.
     *
     * SHOULD also validate & filter the request's content, and throw a
     * RequestException when validation fails.
     */
    public function parseHttpRequest(ServerHttpRequest $httpRequest, array $attributes) : RequestInterface;
}
