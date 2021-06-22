<?php

namespace Flowgistics\XML\Transformers;

use Flowgistics\XML\Data\XMLObject;

trait Transformable
{
    /**
     * @psalm-var list<Transformer|class-string<Transformer>>
     */
    protected array $transformers = [];

    /**
     * Add a transformer to the output.
     *
     * @param Transformer|string $transformer
     * @psalm-param Transformer|class-string<Transformer> $transformer
     *
     * @return self
     */
    public function addTransformer(Transformer | string $transformer): self
    {
        $this->transformers[] = $transformer;

        return $this;
    }

    /**
     * Apply the registered transformers on the input.
     *
     * @param XMLObject $on - input to apply the transformers on
     *
     * @return XMLObject|array - the transformed input
     */
    private function applyTransformers(XMLObject $on): XMLObject | array
    {
        foreach ($this->getTransformers() as $transformer) {
            /** @var XMLObject|array $on */
            $on = $transformer::apply($on);
        }

        return $on;
    }

    /**
     * Get the transformers.
     *
     * @return Transformer[]
     * @psalm-return list<Transformer|class-string<Transformer>>
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }
}
