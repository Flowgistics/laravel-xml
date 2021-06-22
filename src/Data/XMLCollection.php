<?php

namespace Flowgistics\XML\Data;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Flowgistics\XML\Casts\Cast;
use Flowgistics\XML\Casts\PendingCast;
use Flowgistics\XML\Transformers\PendingTransform;
use Flowgistics\XML\Transformers\Transformable;
use Flowgistics\XML\Transformers\Transformer;
use Flowgistics\XML\XML;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;

class XMLCollection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use Transformable;

    protected string $optimize = XML::OPTIMIZE_NONE;

    private XMLObject $items;

    private XMLElement $raw;

    public function __construct(XMLElement $items)
    {
        $this->raw = $items;
        $this->items = new XMLObject((array) $items);
    }

    /**
     * Returns the raw xml data.
     *
     * @return XMLElement
     */
    public function raw(): XMLElement
    {
        return $this->raw;
    }

    /**
     * Get the xml as a collection.
     *
     * @return Collection
     */
    public function collect(): Collection
    {
        return new Collection($this->items);
    }

    /**
     * Pass overloaded methods to the items.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->items->{$name}(...$arguments);
    }

    /**
     * Get a item from the xml.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->items->{$key};
    }

    /**
     * Update a value in the XML.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, mixed $value): void
    {
        $this->items->{$name} = $value;
    }

    /**
     * Check if an item in the xml isset.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->items->{$name});
    }

    /**
     * Alias for transform.
     *
     * @see transform
     */
    public function expect(string $key): PendingTransform
    {
        return $this->transform($key);
    }

    /**
     * Start a transform for the given key.
     *
     * @param string $key
     *
     * @return PendingTransform
     */
    public function transform(string $key): PendingTransform
    {
        return new PendingTransform(/**
         * @param string|callable|Transformer $transformer
         * @psalm-param class-string<Transformer>|callable|Transformer $transformer
         * @return $this
         */ function (string | callable | Transformer $transformer) use ($key): mixed {
            $this->items[ $key ] = is_callable($transformer) ?
                $transformer($this->items[ $key ])
                : $transformer::apply($this->items[ $key ]);

            return $this;
        });
    }

    /**
     * Start a cast for the given key.
     *
     * @param string $key
     *
     * @return PendingCast
     */
    public function cast(string $key): PendingCast
    {
        return new PendingCast(function (string $cast) use ($key) {
            if (is_array($this->items[ $key ])) {
                $this->items[ $key ] = array_map(static function (mixed $item) use ($cast): mixed {
                    return Cast::to((array) $item, $cast);
                }, $this->items[ $key ]);

                return $this;
            }
            $this->items[ $key ] = Cast::to((array) $this->items[ $key ], $cast);

            return $this;
        });
    }

    public function optimize(string $type = XML::OPTIMIZE_UNDERSCORE): self
    {
        $this->optimize = $type;

        return $this;
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed        $offset
     *
     * @psalm-param string $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items->toArray());
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed        $offset
     *
     * @psalm-param string $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[ $offset ];
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed             $offset
     * @param mixed             $value
     *
     * @psalm-param string|null $offset
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[ $offset ] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param mixed        $offset
     *
     * @psalm-param string $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[ $offset ]);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items->toArray());
    }

    /**
     * Convert the collection to its string representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_map(static function ($value): mixed {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            }

            if ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->getAsArray());
    }

    /**
     * Get the xml.
     *
     * @return XMLObject|array
     */
    public function get(): XMLObject | array
    {
        $items = $this->applyOptimize();

        return $this->applyTransformers($items);
    }

    /**
     * Get the xml.
     *
     * @return array
     */
    public function getAsArray(): array
    {
        $output = $this->get();

        return $output instanceof XMLObject ? $output->toArray() : $output;
    }

    /**
     * Apply to optimization.
     *
     * @return XMLObject
     */
    private function applyOptimize(): XMLObject
    {
        if ($this->optimize === XML::OPTIMIZE_UNDERSCORE) {
            $method = static fn (string $key): string => Str::snake(str_replace('.', '_', $key));
        } elseif ($this->optimize === XML::OPTIMIZE_CAMELCASE) {
            $method = static fn (string $key): string => Str::camel(str_replace('.', '_', $key));
        } else {
            return $this->items;
        }

        return new XMLObject($this->loopOptimize($this->items->toArray(), $method));
    }

    /**
     * Recursively optimize the xml using the chosen method.
     *
     * @param array                        $items
     * @param Closure                      $callback
     *
     * @psalm-param Closure(string):string $callback
     *
     * @return array
     */
    private function loopOptimize(array $items, Closure $callback): array
    {
        $data = [];
        if (! count($items)) {
            return [];
        }
        /** @var int|string|object|mixed[] $value */
        foreach ($items as $key => $value) {
            $itemKey = $callback((string) $key);
            if (is_object($value)) {
                if (str_contains($value::class, 'XMLElement')) {
                    $data[ $itemKey ] = new XMLObject($this->loopOptimize((array) $value, $callback));
                }
            } elseif (is_array($value)) {
                $data[ $itemKey ] = $this->loopOptimize($value, $callback);
            } else {
                $data[ $itemKey ] = $value;
            }
        }

        return $data;
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $callback = fn (mixed $value): mixed => $value instanceof Arrayable ? $value->toArray() : $value;

        return array_map($callback, $this->getAsArray());
    }
}
