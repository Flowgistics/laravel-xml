<?php

namespace Flowgistics\XML\Transformers;

use Closure;
use Flowgistics\XML\Exceptions\UnknownTransformException;

class PendingTransform
{
    /**
     * @var array|string[]
     * @psalm-var array<string, class-string<Transformer>>
     */
    protected array $transformAliases = [
        'array' => ArrayTransformer::class,
    ];

    /**
     * Create a new PendingTransform.
     *
     * @param Closure $resolve
     */
    public function __construct(private Closure $resolve)
    {
    }

    /**
     * Transform using a alias.
     *
     * @param string $alias
     *
     * @return mixed
     * @throws UnknownTransformException
     */
    public function as(string $alias): mixed
    {
        if (! array_key_exists($alias, $this->transformAliases)) {
            throw UnknownTransformException::unknownAlias($alias);
        }

        return $this->with($this->transformAliases[$alias]);
    }

    /**
     * Transform and resolve using a transformer.
     *
     * @param string $transformer
     *
     * @return mixed
     */
    public function with(string $transformer): mixed
    {
        $resolve = $this->resolve;

        return $resolve($transformer);
    }
}
