<?php

require_once '../vendor/autoload.php';

use Flowgistics\XML\Data\XMLElement;
use Flowgistics\XML\Transformers\ArrayTransformer;
use Flowgistics\XML\Transformers\Transformer;
use Flowgistics\XML\XML;

// We offer a array transformer by default that wraps the item with array_wrap

// ===============
// Simple array transform using build in alias
// ===============

$xml = XML::import('notes.xml')
    ->expect('note')->as('array')
    ->get();

// is the same as

$xml = XML::import('notes.xml')
    ->transform('note')->with(ArrayTransformer::class)
    ->get();

// $xml->note will now always be a array

// ===============
//Custom transformer
// ===============

// Example transformer to filter only the completed notes
class CompletedNoteFilter implements Transformer
{
    /**
     * Filter only the completed notes.
     *
     * @param mixed $data
     *
     * @return array|mixed
     */
    public static function apply($data)
    {
        return array_filter($data, function ($note) {
            /*
             * @var $note XMLElement
             */
            return $note->attribute('completed', false) === 'true';
        });
    }
}

$xml = XML::import('notes-2.xml')
    ->transform('note')->with(CompletedNoteFilter::class)
    ->get();

// $xml->note now only has notes that are completed
