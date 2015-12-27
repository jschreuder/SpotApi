<?php

namespace Spot\Api\Message;

trait AttributesArrayAccessTrait
{
    private $attributes;

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function offsetExists($offset) : bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet($offset)
    {
        if (!isset($this[$offset])) {
            throw new \OutOfBoundsException('No such offset: ' . $offset);
        }
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
