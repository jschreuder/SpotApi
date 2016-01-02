<?php

namespace spec\Spot\Api\Message;

/** @mixin  \Spot\Api\Message\AttributesArrayAccessTrait */
trait AttributesArrayAccessSpecTrait
{
    public function it_implements_array_access()
    {
        $this->offsetExists('test')
            ->shouldReturn(false);
        $this['test'] = 42;
        $this->offsetExists('test')
            ->shouldReturn(true);
        $this->offsetGet('test')
            ->shouldReturn(42);
        unset($this['test']);
        $this->offsetExists('test')
            ->shouldReturn(false);
    }

    public function it_can_get_at_its_attributes()
    {
        $attributes = $this->getAttributes();
        $attributes->shouldBeArray();

        $array = array_merge($attributes->getWrappedObject(), ['test' => 'mest', 'vest' => 'rest']);
        $this['test'] = $array['test'];
        $this['vest'] = $array['vest'];

        $this->getAttributes()->shouldReturn($array);
    }

    public function it_errors_on_unknown_keys()
    {
        $this->shouldThrow(\OutOfBoundsException::class)->duringOffsetGet('i-do-not-exist');
    }
}
