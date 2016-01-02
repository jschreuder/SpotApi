<?php declare(strict_types = 1);

namespace Spot\Api\Middleware;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Spot\Api\ApplicationInterface;
use Spot\Api\Response\Http\JsonApiErrorResponse;

class JsonApiRequestParser implements ApplicationInterface
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
        // Only works on requests with non-empty JSON bodies
        $body = $httpRequest->getBody()->getContents();
        if ($httpRequest->getHeaderLine('Content-Type') !== 'application/vnd.api+json' || empty($body)) {
            return $this->application->execute($httpRequest);
        }

        $parsedBody = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonApiErrorResponse([
                [
                    'title' => 'Invalid JSON, couldn\'t decode.',
                    'status' => '400',
                ],
            ], 400);
        }

        return $this->application->execute($httpRequest->withParsedBody($parsedBody));
    }
}
