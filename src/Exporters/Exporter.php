<?php

namespace Flowgistics\XML\Exporters;

interface Exporter
{
    public function toString(): string;

    public function toFile(string $path): void;
}
