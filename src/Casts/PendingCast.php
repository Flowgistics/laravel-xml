<?php

namespace Flowgistics\XML\Casts;

use Closure;

class PendingCast
{
    /**
     * Create a new PendingTransform.
     *
     * @param Closure $resolve
     */
    public function __construct(private Closure $resolve)
    {
    }

    /**
     * Transform and resolve using a transformer.
     *
     * @param $cast
     *
     * @return mixed
     */
    public function to(string $cast)
    {
        $resolve = $this->resolve;

        return $resolve($cast);
    }
}
