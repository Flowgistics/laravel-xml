<?php

namespace Flowgistics\XML\Tests\Features\Import\example;

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
