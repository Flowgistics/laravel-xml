<?php

namespace Flowgistics\XML\Tests\Features\Export;

use Flowgistics\XML\Tests\TestCase;
use Flowgistics\XML\XML;
use Illuminate\Support\Facades\View;

class ExportFromViewTest extends TestCase
{
    protected $data = [
        'files' => [
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

    public function setUp(): void
    {
        parent::setUp();
        View::addLocation(__DIR__.'/views');
    }

    /**
     * Test xml export from a view.
     */
    public function test_exports_from_view()
    {
        $xml = XML::exportView('files', $this->data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);

        $path = 'files.xml';
        XML::exportView('files', $this->data)
            ->setRootTag('files')
            ->version('1.0')
            ->encoding('UTF-8')
            ->toFile($path);

        $this->assertFileExists($path);
        unlink($path);
    }

    /**
     * Test xml from a view without a generated root tag.
     */
    public function test_exports_from_view_without_root()
    {
        $xml = XML::exportView('no-root', $this->data)
            ->disableRootTag()
            ->toString();
        $this->assertMatchesXmlSnapshot($xml);
    }
}
