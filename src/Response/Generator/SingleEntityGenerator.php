<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Spot\Api\Response\ResponseInterface;
use Tobscure\JsonApi\ElementInterface;
use Tobscure\JsonApi\Resource;

class SingleEntityGenerator extends AbstractSerializingGenerator
{
    protected function validResponse(ResponseInterface $response) : bool
    {
        return isset($response['data']);
    }

    protected function generateData(ResponseInterface $response) : ElementInterface
    {
        return new Resource($response['data'], $this->getSerializer());
    }
}
