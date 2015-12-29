<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LogLevel;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class SingleEntityGenerator extends AbstractSerializingGenerator
{
    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        if (!isset($response['data'])) {
            $this->log(LogLevel::ERROR, 'No data present in Response.');
            return new JsonApiErrorResponse([
                'title' => 'Server Error: no data to generate response from',
                'status' => '500',
            ], 500);
        }

        $resource = (new Resource($response['data'], $this->getSerializer()))
            ->with(isset($response['includes']) ? $response['includes'] : []);
        $document = new Document($resource);
        foreach ($this->metaDataGenerator($response) as $key => $value) {
            $document->addMeta($key, $value);
        }
        return new JsonApiResponse($document);
    }
}
