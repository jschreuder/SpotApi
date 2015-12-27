<?php declare(strict_types = 1);

namespace Spot\Api\Response\Generator;

use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Log\LogLevel;
use Spot\Api\Response\Http\JsonApiErrorResponse;
use Spot\Api\Response\Http\JsonApiResponse;
use Spot\Api\Response\Message\ResponseInterface;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;

class MultiEntityGenerator extends SingleEntityGenerator
{
    public function generateResponse(ResponseInterface $response) : HttpResponse
    {
        if (!isset($response['data']) || !is_array($response['data'])) {
            $this->log(LogLevel::ERROR, 'No set of data present in Response.');
            return new JsonApiErrorResponse([
                'title' => 'Server Error: no data to generate response from',
                'status' => '500',
            ], 500);
        }

        $collection = (new Collection($response['data'], $this->getSerializer()))
            ->with(isset($response['includes']) ? $response['includes'] : []);
        $document = new Document($collection);
        foreach ($this->metaDataGenerator($response) as $key => $value) {
            $document->addMeta($key, $value);
        }
        return new JsonApiResponse($document);
    }
}
