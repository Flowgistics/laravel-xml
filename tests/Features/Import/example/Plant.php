<?php

namespace Flowgistics\XML\Tests\Features\Import\example;

use Flowgistics\XML\Casts\Castable;

class Plant implements Castable
{
    public $common = 'Foo';

    public $botanical = 'Bar';

    public $zone = -1;

    public $light = 'Baz';

    public $price = -1;

    public $availability = -1;

    public function __construct($common, $botanical, $zone, $light, $price, $availability)
    {
        $this->common = $common;
        $this->botanical = $botanical;
        $this->zone = $zone;
        $this->light = $light;
        $this->price = $price;
        $this->availability = $availability;
    }

    public static function fromCast(array $data): Castable
    {
        return new Plant(...array_values($data));
    }
}
