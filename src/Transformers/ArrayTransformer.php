<?php

namespace Flowgistics\XML\Transformers;

use Illuminate\Support\Arr;

class ArrayTransformer implements Transformer
{
    /**
     * Wrap the data in a array.
     *
     * @param mixed $data
     *
     * @return array
     */
    public static function apply(mixed $data): array
    {
        return Arr::wrap($data);
    }
}
