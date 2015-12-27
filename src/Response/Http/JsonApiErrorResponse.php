<?php declare(strict_types = 1);

namespace Spot\Api\Response\Http;

use Tobscure\JsonApi\Document;

class JsonApiErrorResponse extends JsonApiResponse
{
    /**
     * JsonResponse overloaded Constructor to assign JSON-API Content-Type
     * and create JSON-API formatted response
     */
    public function __construct(array $errors, int $status = 200, array $meta = null)
    {
        $document = new Document();
        $document->setErrors($errors);
        parent::__construct($document, $status);
    }
}
