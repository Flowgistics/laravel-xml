<?php

namespace Flowgistics\XML;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Class XMLBuilder `version`, `rootTag`, `itemName` and `encoding`.
 *
 * @method XMLBuilder version(string $version = "1.0") set the xml version
 * @method XMLBuilder encoding(string $encoding = "UTF-8") set the xml encoding
 * @method XMLBuilder rootTag(string $name = "root") set the name of the root tag
 * @method XMLBuilder itemName(string $name = "item") set the default name for items without a name
 */
class XMLBuilder
{
    /**
     * The default root name.
     */
    protected const DEFAULT_ROOT = 'root';
    /**
     * @var string|bool the name of the root tag. Set to false to disable the root tag.
     */
    protected string | bool $rootTag = self::DEFAULT_ROOT;
    /**
     * @var string the default name of xml items that where not defined with a key.
     */
    protected string $itemName = 'item';
    /**
     * @var array data for the xml
     */
    protected array $data = [];
    /**
     * @var bool force usage of the item name instead a name generated based on the root tag
     */
    protected bool $forceItemName = false;

    /**
     * XMLBuilder constructor.
     *
     * @param string $encoding the encoding to use for the xml document. Defaults to "UTF-8".
     * @param string $version  the version to use for the xml document. Defaults to "1.0".
     */
    public function __construct(protected string $encoding = 'UTF-8', protected string $version = '1.0')
    {
    }

    /**
     * Disable the root tag.
     *
     * @return static
     */
    public function disableRootTag(): static
    {
        return $this->setRootTag(false);
    }

    /**
     * Set the root tag for the document.
     *
     * @param string|bool $tag the name to use as the root tag. Set to `false` to disable.
     *
     * @return static
     */
    public function setRootTag(string | bool $tag): static
    {
        $this->rootTag = $tag;

        return $this;
    }

    /**
     * Set the data.
     *
     * @param array $data
     *
     * @return XMLBuilder
     */
    public function data(array $data): XMLBuilder
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Handle dynamic setters for `version`, `rootTag`, `itemName` and `encoding`.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call(string $name, array $arguments): XMLBuilder
    {
        if (in_array($name, ['version', 'rootTag', 'encoding', 'itemName'])) {
            if (count($arguments) !== 1) {
                throw new InvalidArgumentException("$name requires 1 parameter");
            }
            $this->{$name} = $arguments[0];

            return $this;
        }

        return $this;
    }

    /**
     * Force item name usage.
     *
     * @return XMLBuilder
     */
    public function forceItemName(): XMLBuilder
    {
        $this->forceItemName = true;

        return $this;
    }

    /**
     * Make the XML Prolog tag.
     *
     * @return string
     */
    protected function getProlog(): string
    {
        return sprintf('<?xml version="%s" encoding="%s"?>', $this->version, $this->encoding) . PHP_EOL;
    }

    /**
     * Make the root tag. Returns `null` if the root tag is disabled.
     *
     * @return string
     */
    protected function openRootTag(): string
    {
        return ! $this->rootTag ? '' : "<$this->rootTag>";
    }

    /**
     * Make the closing tag for the root tag. Returns `null` if the root tag is disabled.
     *
     * @return string
     */
    protected function closeRootTag(): string
    {
        return ! $this->rootTag ? '' : "</$this->rootTag>";
    }

    protected function getRootTag(): string
    {
        return is_string($this->rootTag) ? $this->rootTag : static::DEFAULT_ROOT;
    }

    /**
     * Generates the name for top-level tags.
     *
     * Primarily used for simple arrays that just contain values without keys.
     * If $field is a string we just return that.
     *
     * If $field is the index of generator loop we check if the root tag is the default "root",
     * in that case the name of the tag will be "item". If the root tag is a custom name we
     * get the singular form of the root name
     *
     * @param string|int $field - name or index the check
     *
     * @return string - the generated name
     */
    protected function getFieldName(string | int $field): string
    {
        if (! is_string($field)) {
            $useItemName = $this->rootTag === static::DEFAULT_ROOT || $this->forceItemName;

            return $useItemName ? $this->itemName : Str::singular($this->getRootTag());
        }

        return $field;
    }

    /**
     * Generates the name for fields where the name is a number.
     *
     * If `forceItemName` is enabled this will return the `itemName` config value.
     * Otherwise it will try to use the singular version of $field
     *
     * @param string $field
     *
     * @return string
     */
    protected function generateFieldName(string $field): string
    {
        return $this->forceItemName ? $this->itemName : Str::singular($field);
    }
}
