<?php

namespace Flowgistics\XML\Data;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;

class XMLObject implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    protected array $items = [];

    protected ?array $attributes = null;

    /**
     * XMLDocument constructor.
     *
     * @param mixed[] $xml
     */
    public function __construct(array $xml)
    {
        if (isset($xml['@attributes']) && is_array($xml['@attributes'])) {
            $this->attributes = $xml['@attributes'];
            unset($xml['@attributes']);
        }
        $this->items = $xml;
    }

    /**
     * Get a attribute by name.
     *
     * @param string     $name    - name of the attribute to get
     * @param null|mixed $default - default value if the attribute does not exist
     *
     * @return mixed
     */
    public function attribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes && $this->hasAttribute($name) ? $this->attributes[ $name ] : $default;
    }

    /**
     * Checks if a attribute is present.
     *
     * @param string $attribute - the name of the attribute
     *
     * @return bool
     */
    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes ?? []);
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
        return $this->items[ $key ];
    }

    /**
     * Set a item in the xml.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->items[ $key ] = $value;
    }

    /**
     * Check if a item exists in the xml.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __isset(string $name): bool
    {
        return isset($this->items[ $name ]);
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
     * @param mixed $offset
     *
     * @psalm-param string $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $offset
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
     * @param mixed $offset
     * @param mixed $value
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
        return new ArrayIterator($this->items);
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
        return array_map(function ($value) {
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
        }, $this->get());
    }

    private function get(): array
    {
        return array_merge([
            '@attributes' => $this->attributes,
        ], $this->items);
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->get());
    }
}
