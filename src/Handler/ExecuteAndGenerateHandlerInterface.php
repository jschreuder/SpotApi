<?php declare(strict_types = 1);

namespace Spot\Api\Handler;

use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Response\Generator\GeneratorInterface;

/**
 * A "handler" takes care of at least two stages of the request->response handling. This handler
 * takes care of Request execution and PSR-7 Response generation based on Executor's Response.
 */
interface ExecuteAndGenerateHandlerInterface extends ExecutorInterface, GeneratorInterface
{
}
