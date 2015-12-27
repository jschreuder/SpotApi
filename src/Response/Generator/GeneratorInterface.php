<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Spot\Api\Response\Message\ResponseInterface;

interface GeneratorInterface
{
    /**
     * Takes a Response message (and HTTP request for reference) and generates
     * a HTTP response.
     *
     * Exceptions will be turned into ServerError responses.
     */
    public function generateResponse(ResponseInterface $response) : HttpResponse;
}
