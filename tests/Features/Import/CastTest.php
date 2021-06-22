<?php

namespace Flowgistics\XML\Tests\Features\Import;

use Flowgistics\XML\Tests\Features\Import\example\Note;
use Flowgistics\XML\Tests\Features\Import\example\Plant;
use Flowgistics\XML\Tests\TestCase;
use Flowgistics\XML\XML;

class CastTest extends TestCase
{
    public function test_can_cast_to_model()
    {
        $path = __DIR__.'/stubs/notes.xml';
        $note = XML::import($path)
            ->cast('note')->to(Note::class)
            ->get()->note;

        $this->assertInstanceOf(Note::class, $note);
        $this->assertEquals('Foo', $note->to);
        $this->assertEquals('Bar', $note->from);
    }

    /**
     * @requires PHPUnit 7.5.1
     */
    public function test_can_cast_and_transform()
    {
        $path = __DIR__.'/stubs/notes.xml';
        $note = XML::import($path)
            ->cast('note')->to(Note::class)
            ->expect('note')->as('array')
            ->get()->note;

        $this->assertIsArray($note);
        $this->assertInstanceOf(Note::class, $note[0]);
        $this->assertEquals('Foo', $note[0]->to);
        $this->assertEquals('Bar', $note[0]->from);
    }

    public function test_can_cast_to_castable()
    {
        $path = __DIR__.'/stubs/plants.xml';

        $plant = XML::import($path)
            ->cast('PLANT')->to(Plant::class)
            ->get()->PLANT[0];

        $this->assertInstanceOf(Plant::class, $plant);
        $this->assertEquals('Bloodroot', $plant->common);
        $this->assertEquals('$2.44', $plant->price);
    }
}
