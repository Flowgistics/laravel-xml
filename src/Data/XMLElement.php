<?php

namespace Flowgistics\XML\Data;

use SimpleXMLElement;

class XMLElement extends SimpleXMLElement
{
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
        return ! is_null($this->attributes()->{$name}) ? (string)$this->attributes()->{$name} : $default;
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
        return $this->attributes()->{$attribute} !== null;
    }
}
