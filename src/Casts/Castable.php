<?php

namespace Flowgistics\XML\Casts;

interface Castable
{
    /**
     * Invoked every time this class was cast to XMLElement.
     *
     * @param array $data
     *
     * @return Castable
     */
    public static function fromCast(array $data): self;
}
