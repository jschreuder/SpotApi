<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\ElementInterface;

class MultiEntityGenerator extends AbstractSerializingGenerator
{
    protected function validResponse(ResponseInterface $response) : bool
    {
        return isset($response['data']) && is_array($response['data']);
    }

    protected function generateData(ResponseInterface $response) : ElementInterface
    {
        return (new Collection($response['data'], $this->getSerializer()))
            ->with(isset($response['includes']) ? $response['includes'] : []);
    }
}
