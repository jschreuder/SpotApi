<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class SingleEntityGenerator extends AbstractSerializingGenerator
{
    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        if (!isset($response['data'])) {
            return $this->noDataResponse();
        }

        $resource = (new Resource($response['data'], $this->getSerializer()))
            ->with(isset($response['includes']) ? $response['includes'] : []);

        $document = new Document($resource);
        $this->generateMetaData($response, $document);

        return new JsonApiResponse($document);
    }
}
