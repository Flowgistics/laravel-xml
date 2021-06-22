<?php

namespace Flowgistics\XML\Transformers;

/**
 * Transformers allow data to be transformed before being send to the user.
 */
interface Transformer
{
    /**
     * Apply is invoked before the final output is send to the user.
     *
     * @param mixed $data - the xml data
     *
     * @return mixed
     */
    public static function apply(mixed $data): mixed;
}
