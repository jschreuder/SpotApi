<?php declare(strict_types = 1);

namespace Spot\Api\Request\Executor;

use Spot\Api\Request\Message\RequestInterface;
use Spot\Api\Response\Message\ResponseInterface;

interface ExecutorInterface
{
    /**
     * Takes a Request message (and HTTP request for reference) and executes it
     * to get the result in a Response message.
     *
     * MUST catch all exceptions internally and throw only ResponseException
     * instances.
     */
    public function executeRequest(RequestInterface $request) : ResponseInterface;
}
