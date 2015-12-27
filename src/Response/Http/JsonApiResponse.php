<?php declare(strict_types = 1);

namespace Spot\Api\Response\Http;

use Tobscure\JsonApi\Document;
use Zend\Diactoros\Response\JsonResponse;

class JsonApiResponse extends JsonResponse
{
    /**
     * JsonResponse overloaded Constructor to assign JSON-API Content-Type
     * and require JsonApi\Document instance as body
     */
    public function __construct(
        Document $document,
        $status = 200,
        array $headers = [],
        $encodingOptions = self::DEFAULT_JSON_FLAGS
    ) {
        $headers['Content-Type'] = 'application/vnd.api+json';
        parent::__construct($document->toArray(), $status, $headers, $encodingOptions);
    }
}
