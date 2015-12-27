<?php declare(strict_types = 1);

namespace Spot\Api\Request;

use Spot\Api\Request\Message\RequestInterface;

class RequestException extends \RuntimeException
{
    /** @var  RequestInterface */
    private $request;

    public function __construct(string $reason, RequestInterface $request)
    {
        $this->request = $request;
        parent::__construct($reason);
    }

    public function getRequestObject() : RequestInterface
    {
        return $this->request;
    }
}
