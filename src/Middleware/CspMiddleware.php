<?php declare(strict_types = 1);

namespace Spot\Api\Middleware;

use ParagonIE\CSPBuilder\CSPBuilder;
use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;
use Spot\Api\ApplicationInterface;

class CspMiddleware implements ApplicationInterface
{
    /** @var  ApplicationInterface */
    private $application;

    /** @var  CSPBuilder */
    private $cspBuilder;

    public function __construct(ApplicationInterface $application, string $configFile)
    {
        $this->application = $application;
        $this->cspBuilder = CSPBuilder::fromFile($configFile);
    }

    public function execute(ServerHttpRequest $httpRequest) : HttpResponse
    {
        $response = $this->application->execute($httpRequest);
        return $this->cspBuilder->injectCSPHeader($response);
    }
}
