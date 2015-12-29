<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;

class MultiEntityGenerator extends AbstractSerializingGenerator
{
    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        if (!isset($response['data']) || !is_array($response['data'])) {
            return $this->noDataResponse();
        }

        $collection = (new Collection($response['data'], $this->getSerializer()))
            ->with(isset($response['includes']) ? $response['includes'] : []);

        $document = new Document($collection);
        $this->generateMetaData($response, $document);

        return new JsonApiResponse($document);
    }
}
