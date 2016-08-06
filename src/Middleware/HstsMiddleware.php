<?php declare(strict_types = 1);

namespace Spot\Api\Middleware;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Psr\Http\Message\UriInterface;
use Spot\Api\ApplicationInterface;
use Zend\Diactoros\Response;

class HstsMiddleware implements ApplicationInterface
{
    /** @var  ApplicationInterface */
    private $application;

    /** @var  int */
    private $maxAge;

    /** @var  bool */
    private $includeSubDomains;

    /** @var  bool */
    private $preload;

    public function __construct(
        ApplicationInterface $application,
        int $maxAge = 31536000,
        bool $includeSubDomains = true,
        bool $preload = true
    )
    {
        $this->application = $application;
        $this->maxAge = $maxAge;
        $this->includeSubDomains = $includeSubDomains;
        $this->preload = $preload;
    }

    public function execute(ServerHttpRequest $httpRequest) : HttpResponse
    {
        // When on HTTP: force redirect to HTTPS
        $uri = $httpRequest->getUri();
        if ($uri->getScheme() === 'http') {
            return $this->httpsRedirect($uri);
        }

        // Execute request using the decorated application
        $httpResponse = $this->application->execute($httpRequest);

        return $httpResponse->withHeader('Strict-Transport-Security', $this->buildHeaderValue());
    }

    private function httpsRedirect(UriInterface $uri) : ResponseInterface
    {
        return new Response('php://memory', 307, [
            'Location' => strval($uri->withScheme('https')->withPort(443)),
        ]);
    }

    private function buildHeaderValue() : string
    {
        $value = 'max-age=' . strval($this->maxAge);
        if ($this->includeSubDomains) {
            $value .= '; includeSubDomains';
        }
        if ($this->preload) {
            $value .= '; preload';
        }
        return $value;
    }
}
