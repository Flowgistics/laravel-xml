<?php

namespace Flowgistics\XML;

use Flowgistics\XML\Data\XMLCollection;
use Flowgistics\XML\Data\XMLElement;
use Flowgistics\XML\Exporters\ArrayExporter;
use Flowgistics\XML\Exporters\ViewExporter;

/**
 * A Laravel XML Import & Export package.
 */
class XML
{
    /**
     * Optimize with underscores type.
     */
    public const OPTIMIZE_UNDERSCORE = 'underscore';

    /**
     * Optimize as camelCase type.
     */
    public const OPTIMIZE_CAMELCASE = 'camelcase';

    /**
     * No optimization
     */
    public const OPTIMIZE_NONE = 'none';

    /**
     * Export a array to xml.
     *
     * @param array $data - the data to export
     *
     * @return ArrayExporter
     */
    public static function export(array $data): ArrayExporter
    {
        return new ArrayExporter($data);
    }

    /**
     * Export a view to laravel.
     *
     * @param string $viewName - the name of the view
     * @param array  $data     - the data to pass to the view
     *
     * @return ViewExporter
     */
    public static function exportView(string $viewName, array $data = []): ViewExporter
    {
        return new ViewExporter($viewName, $data);
    }

    /**
     * Import a xml file from a path.
     *
     * @param string $path - the path of the xml file. Can be a url/
     *
     * @param bool   $raw  - set to true to return raw xml data
     *
     * @return XMLCollection|XMLElement
     * @throws \Exception
     */
    public static function import(string $path, bool $raw = false): Data\XMLElement | Data\XMLCollection
    {
        $import = new XMLImporter($path);

        return $raw ? $import->raw() : $import->get();
    }
}
