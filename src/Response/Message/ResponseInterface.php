<?php declare(strict_types = 1);

namespace Spot\Api\Response\Message;

interface ResponseInterface extends \ArrayAccess
{
    public function getResponseName() : string;

    public function getAttributes() : array;

    public function getContentType() : string;
}
