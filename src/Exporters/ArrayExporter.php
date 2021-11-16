<?php

namespace Flowgistics\XML\Exporters;

use DOMDocument;
use DOMNode;
use Flowgistics\XML\XMLBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ArrayExporter extends XMLBuilder implements Exporter
{
    private bool $prettyOutput = false;

    /**
     * ArrayExporter constructor.
     *
     * @param array $data - data to use
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $this->data = $data;
    }

    /**
     *  When used the XML will be formatted when outputted.
     *
     * @return ArrayExporter
     */
    public function usePrettyOutput(): self
    {
        $this->prettyOutput = true;

        return $this;
    }

    /**
     * Save the xml to a file.
     *
     * @param string $path - the path to the file
     */
    public function toFile(string $path): void
    {
        File::put($path, $this->toString());
    }

    /**
     * Generate xml based on a array.
     *
     * @param bool|null $prettyOutput when true the outputted XML will be formatted.
     *
     * @return string
     */
    public function toString(?bool $prettyOutput = null): string
    {
        $document = new DOMDocument($this->version, $this->encoding);
        $root = $document->documentElement;

        if ($this->rootTag && is_string($this->rootTag)) {
            $xmlRoot = $document->createElement($this->rootTag);
            $root = $document->appendChild($xmlRoot);
        }

        /** @var int|string|mixed[] $value */
        foreach ($this->data as $field => $value) {
            if (is_array($value)) {
                $document = $this->walkArray($value, (string) $field, $document, $root);

                continue;
            }
            $field = $this->getFieldName($field);
            $element = $document->createElement($field, (string) $value);
            $root->appendChild($element);
        }

        if ($this->prettyOutput || $prettyOutput) {
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
        }

        return $document->saveXML();
    }

    /**
     * Walk over a array of values and add those values to the xml.
     *
     * @param array       $values   - values to walk over
     * @param string      $name     - name of the parent element
     * @param DOMDocument $document - the xml document
     * @param DOMNode     $root     - the root element of the xml document
     *
     * @return DOMDocument
     */
    private function walkArray(array $values, string $name, DOMDocument $document, DOMNode $root): DOMDocument
    {
        $rootElement = $document->createElement($name);

        /** @var int|string|mixed[] $value */
        foreach ($values as $fieldName => $value) {
            if (! is_string($fieldName)) {
                $fieldName = $this->getFieldName($name);
            }
            if (is_array($value)) {
                $element = $document->createElement($fieldName);

                if (empty($value)) {
                    $element = $document->createElement($fieldName);
                    $parent = $rootElement->appendChild($element);
                } elseif (! Arr::isAssoc($value)) {
                    if ($rootElement->parentNode === null) {
                        $element = $document->createElement($name);
                        $parent = $root->appendChild($element);
                    } else {
                        $element = $document->createElement($fieldName);
                        $parent = $rootElement->appendChild($element);
                    }
                } else {
                    $parent = $root->appendChild($element);
                }

                $this->createMultiple($fieldName, $value, $document, $parent);

                continue;
            }
            $element = $document->createElement($fieldName, (string) $value);
            if (! Arr::isAssoc($values)) {
                $root->appendChild($element);
            } else {
                $rootElement->appendChild($element);
                $root->appendChild($rootElement);
            }
        }

        return $document;
    }

    /**
     * Recursively create multiple xml children with the same name.
     *
     * @param string      $name     - the name of the children
     * @param array       $values   - values for the children
     * @param DOMDocument $document - the xml document
     * @param DOMNode     $parent   - the parent element the children belong to
     */
    private function createMultiple(string $name, array $values, DOMDocument $document, DOMNode $parent): void
    {
        /** @var int|string|mixed[] $value */
        foreach ($values as $field => $value) {
            if (is_array($value)) {
                $child = $parent;
                if (is_string($field)) {
                    $element = $document->createElement($field);
                    $child = $parent->appendChild($element);
                    $name = $field;
                }

                $this->createMultiple($name, $value, $document, $child);

                continue;
            }
            if (is_numeric($field)) {
                $field = $this->generateFieldName($name);
            }

            $element = $document->createElement($field, (string) $value);
            $parent->appendChild($element);
        }
    }
}
