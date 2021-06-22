<?php

namespace Flowgistics\XML;

use Illuminate\Support\ServiceProvider;

class XMLServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind('XML', XML::class);
    }
}
