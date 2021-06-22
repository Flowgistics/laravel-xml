<?php

namespace Flowgistics\XML\Tests\Unit;

use Flowgistics\XML\Tests\TestCase;
use Flowgistics\XML\XML;

class XMLBuilderTest extends TestCase
{
    /**
     * Test can set the xml version.
     */
    public function test_can_set_version()
    {
        $xml = XML::export([])
            ->version('2.0')
            ->toString();

        $this->assertEquals("<?xml version=\"2.0\" encoding=\"UTF-8\"?>\n<root/>\n", $xml);
    }

    /**
     * Test can set the xml encoding.
     */
    public function test_can_set_encoding()
    {
        $xml = XML::export([])
            ->encoding('iso-8859-1')
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test can set the root tag using the setter.
     */
    public function test_can_set_root_using_setter()
    {
        $xml = XML::export([])
            ->setRootTag('test')
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test can set the root tag using the dynamic setter.
     */
    public function test_can_set_root_using_dynamic()
    {
        $xml = XML::export([])
            ->rootTag('dynamic')
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test can disable the root tag.
     */
    public function test_can_disable_root()
    {
        $xml = XML::export([])
            ->disableRootTag()
            ->toString();

        $xml = trim($xml); // remove format

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    /**
     * Test can set custom item name.
     */
    public function test_can_set_item_name()
    {
        $xml = XML::export(['foo', 'bar', 'baz'])
            ->itemName('name')
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test custom item name generation.
     */
    public function test_generates_item_name()
    {
        $xml = XML::export(['foo', 'bar', 'baz'])
            ->rootTag('names')
            ->itemName('entry')
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test force item name.
     */
    public function test_can_disable_item_name_generation()
    {
        $xml = XML::export(['foo', 'bar', 'baz'])
            ->rootTag('names')
            ->itemName('entry')
            ->forceItemName()
            ->toString();

        $this->assertMatchesXmlSnapshot($xml);
    }

    /**
     * Test that wrong called dynamic setters give a exception.
     */
    public function test_dynamic_setter_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        XML::export([])->version();
        XML::export([])->encoding(1, 2, 3);
    }
}
