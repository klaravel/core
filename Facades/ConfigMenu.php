<?php

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ConfigMenu extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'configMenu';
    }
}
