<?php

require_once '../vendor/autoload.php';

use Flowgistics\XML\Casts\Castable;
use Flowgistics\XML\XML;

// ===============
// Model cast
// ===============

class Note extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'to',
        'from',
        'heading',
        'body',
        'completed_at',
    ];
}

$xml = XML::import('notes.xml')
    ->cast('note')->to(Note::class)
    ->get();

// $xml->note is now a Note model instance

// ===============
// Castable cast
// ===============

class TextNote implements Castable
{
    public function __construct($to, $from, $text)
    {
        // ...
    }

    public static function fromCast(array $data): Castable
    {
        return new TextNote($data['to'], $data['from'], $data['body']);
    }
}

$xml = XML::import('notes.xml')
    ->cast('note')->to(TextNote::class)
    ->get();

// ===============
// Default cast
// ===============

class MyNote
{
    public function __construct($data)
    {
        // ...
    }
}

$xml = XML::import('notes.xml')
    ->cast('note')->to(MyNote::class)
    ->get();
