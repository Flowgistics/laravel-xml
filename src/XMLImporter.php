<?php

namespace Flowgistics\XML;

use Exception;
use Flowgistics\XML\Data\XMLCollection;
use Flowgistics\XML\Data\XMLElement;

class XMLImporter
{
    /**
     * @var XMLElement the loaded xml
     */
    public XMLElement $xml;
    /**
     * @var XMLCollection the processed xml ready to be used
     */
    protected XMLCollection $output;

    /**
     * XMLImporter constructor.
     *
     * @param string $path - path of the xml file to load
     *
     * @throws Exception
     */
    public function __construct(protected string $path)
    {
        $this->load();
    }

    /**
     * Load the xml.
     *
     * @throws Exception
     */
    private function load(): void
    {
        try {
            $this->xml = new XMLElement($this->path, 0, filter_var($this->path, FILTER_VALIDATE_URL) || file_exists($this->path));
            $this->output = new XMLCollection($this->xml);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get the loaded xml.
     *
     * @return XMLCollection
     */
    public function get(): XMLCollection
    {
        return $this->output;
    }

    /**
     * Get the raw unprocessed xml.
     *
     * @return XMLElement
     */
    public function raw(): XMLElement
    {
        return $this->xml;
    }
}
