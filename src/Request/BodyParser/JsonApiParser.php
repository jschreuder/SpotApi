<?php declare(strict_types = 1);

namespace Spot\Api\Request\BodyParser;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Spot\Api\ApplicationInterface;
use Spot\Api\Response\Http\JsonApiErrorResponse;

class JsonApiParser implements ApplicationInterface
{
    /** @var  ApplicationInterface */
    private $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /** {@inheritdoc} */
    public function execute(ServerHttpRequest $httpRequest) : HttpResponse
    {
        // Only works on requests with JSON bodies
        if (strpos($httpRequest->getHeaderLine('Content-Type'), 'application/vnd.api+json') === false) {
            return $this->application->execute($httpRequest);
        }
        $body = $httpRequest->getBody()->getContents();

        // If there's no body, there's nothing to do here
        if (!$body) {
            return $this->application->execute($httpRequest);
        }
        $parsedBody = json_decode($body, true);

        // If an error occurred, duck out now
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonApiErrorResponse([
                'title' => 'Invalid JSON, couldn\'t decode.',
                'status' => '400'
            ], 400);
        }

        // Everything is well, continue on with parsed JSON body
        return $this->application->execute($httpRequest->withParsedBody($parsedBody));
    }
}
