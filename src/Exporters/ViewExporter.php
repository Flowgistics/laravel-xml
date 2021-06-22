<?php

namespace Flowgistics\XML\Exporters;

use Flowgistics\XML\XMLBuilder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\File;

class ViewExporter extends XMLBuilder implements Exporter
{
    protected string $view = '';

    /**
     * ViewExporter constructor.
     *
     * @param string $viewName - name of the view
     * @param array  $data     - data to pass to the view
     */
    public function __construct(string $viewName, array $data = [])
    {
        parent::__construct();

        /** @psalm-var Factory $viewFactory */
        $viewFactory = view();
        $this->view = $viewFactory->make($viewName, $data)->render();
    }

    /**
     * Get the xml as a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return sprintf("%s%s%s%s", $this->getProlog(), $this->openRootTag(), $this->view, $this->closeRootTag());
    }

    /**
     * Save the xml to a file.
     *
     * @param string $path - the path to the file
     */
    public function toFile(string $path): void
    {
        File::put($path, $this->toString());
    }
}
