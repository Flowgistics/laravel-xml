<?php

namespace Flowgistics\XML\Casts;

use Illuminate\Database\Eloquent\Model;

class Cast
{
    /**
     * Cast a array to the given class.
     *
     * @param array $what - values to pass to the cast
     * @param       $to   - class to cast
     *
     * @psalm-param string|class-string|Model|Castable $to
     * @psalm-suppress UnsafeInstantiation,MixedMethodCall,InvalidStringClass
     * @return mixed
     */
    public static function to(array $what, $to): mixed
    {
        $interfaces = class_implements($to);
        if ($to instanceof Model) {
            return new ($to::class)($what);
        }

        if ($to instanceof Castable || isset($interfaces[ Castable::class ])) {
            return $to::fromCast($what);
        }

        return new $to($what);
    }
}
