<?php

namespace Flowgistics\XML\Tests\Features\Export;

use Flowgistics\XML\Tests\TestCase;
use Flowgistics\XML\XML;

class ExportFromDataTest extends TestCase
{
    /**
     * Test exporting a array.
     */
    public function test_exports_from_array()
    {
        $data = [
            'file' => [
                [
                    'name' => 'file1',
                    'type' => 'pdf',
                ],
                [
                    'name' => 'file2',
                    'type' => 'png',
                ],
                [
                    'name' => 'file3',
                    'type' => 'xml',
                ],
            ],
        ];

        $xml = XML::export($data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test exporting a nested array.
     */
    public function test_exports_from_nested_array()
    {
        $data = [
            'file' => [
                [
                    'name' => 'file1',
                    'type' => 'pdf',
                ],
                [
                    'names' => [
                        ['name' => 'file2-1'],
                        ['name' => 'file2-2'],
                        ['name' => 'file2-3'],
                    ],
                    'type' => 'png',
                ],
            ],
        ];

        $xml = XML::export($data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test exporting a single array of strings using a custom root tag.
     */
    public function test_exports_from_string_array()
    {
        $data = [
            'file1',
            'file2',
            'file3',
        ];

        $xml = XML::export($data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test exporting a single array of strings using the default root tag.
     */
    public function test_exports_from_string_array_with_default_root()
    {
        $data = [
            'file1',
            'file2',
            'file3',
        ];

        $xml = XML::export($data)
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test exporting a nested string array.
     */
    public function test_exports_from_nested_string_array()
    {
        $data = [
            'file' => [
                'file1',
                'file2',
                'file3',
            ],
        ];

        $xml = XML::export($data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test exporting a nested string array.
     */
    public function test_exports_from_nested_arrays()
    {
        $data = [
            'key1' => [
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => [],
            ],
            'key2' => [
                'a' => 'b',
                'c' => 'd',
            ],
        ];

        $xml = XML::export($data)
            ->setRootTag('export')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }
}
