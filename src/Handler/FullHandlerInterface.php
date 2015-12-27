<?php declare(strict_types = 1);

namespace Spot\Api\Handler;

use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Request\HttpRequestParser\HttpRequestParserInterface;
use Spot\Api\Response\Generator\GeneratorInterface;

/**
 * A "handler" takes care of at least two stages of the request->response handling. The full handler
 * takes care of all three stages.
 */
interface FullHandlerInterface extends HttpRequestParserInterface, ExecutorInterface, GeneratorInterface
{
}
