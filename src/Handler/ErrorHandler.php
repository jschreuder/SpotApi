<?php declare(strict_types = 1);

namespace Spot\Api\Handler;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Spot\Api\Request\Executor\ExecutorInterface;
use Spot\Api\Request\Message\RequestInterface;
use Spot\Api\Response\Generator\GeneratorInterface;
use Spot\Api\Response\Message\Response;
use Spot\Api\Response\Message\ResponseInterface;
use Spot\Api\Response\Http\JsonApiErrorResponse;

class ErrorHandler implements ExecutorInterface, GeneratorInterface
{
    /** @var  string */
    private $name;

    /** @var  int */
    private $statusCode;

    /** @var  string */
    private $message;

    public function __construct(string $name, int $statusCode, string $message = null)
    {
        $this->name = $name;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function executeRequest(RequestInterface $request) : ResponseInterface
    {
        $attributes = [];
        if (isset($request['errors'])) {
            $attributes['errors'] = $request['errors'];
        }
        return new Response($this->name, $attributes, $request);
    }

    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        $errors = [['title' => $this->message ?: $this->name]];
        if (isset($response['errors'])) {
            $errors = $response['errors'];
        }

        return new JsonApiErrorResponse($errors, $this->statusCode);
    }
}
