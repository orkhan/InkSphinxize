<?php namespace Ink\InkSphinxize\Facades;

use Illuminate\Support\Facades\Facade;

class InkSphinxize extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'inksphinxize'; }

}