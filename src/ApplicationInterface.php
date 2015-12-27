<?php declare(strict_types = 1);

namespace Spot\Api;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as ServerHttpRequest;

interface ApplicationInterface
{
    public function execute(ServerHttpRequest $httpRequest) : HttpResponse;
}
