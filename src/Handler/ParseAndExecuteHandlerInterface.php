<?php declare(strict_types = 1);

namespace Spot\Api\Handler;

use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Request\HttpRequestParser\HttpRequestParserInterface;

/**
 * A "handler" takes care of at least two stages of the request->response handling. This handler
 * takes care of PSR-7 Request to Request parsing and Request execution.
 */
interface ParseAndExecuteHandlerInterface extends HttpRequestParserInterface, ExecutorInterface
{
}
