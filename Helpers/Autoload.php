<?php

/**
 * Custom factory method for modules
 *
 * Example: cfactory('pages', 'Modules\Pages\Http\Page', 100)->create();
 * 
 * @param  string $module 	Module name
 * @return mixed
 */
if(!function_exists('cfactory'))
{
    function cfactory($module)
    {
    	// custom path to package
        $factoryPath = Module::getModulePath($module) . 'Database/factories/';

        if( ! is_dir($factoryPath) ) {
            throw new \InvalidArgumentException("Package name is invalid or \"/src/database/factories/\" is not created: \n");
        }

        $factory = \Illuminate\Database\Eloquent\Factory::construct(
            Faker\Factory::create(),
            // custom path to factories
            $factoryPath
        );

        $arguments = func_get_args();

        if( isset($arguments[2]) && is_string($arguments[2]) ) {
            return $factory->of($arguments[1], $arguments[2])->times(isset($arguments[3]) ? $arguments[3] : 2);
        } elseif( isset($arguments[2]) ) {
            return $factory->of($arguments[1])->times($arguments[2]);
        } else {
            return $factory->of($arguments[1]);
        }
    }
}

/**
 * CDN Helper function
 *
 * Example: cdn('url of assets');
 * 
 * @param  string $assetUrl
 * @return string
 */
if(!function_exists('cdn'))
{
    function cdn($assetUrl)
    {
        return config('core.cdn_url') . $assetUrl;
    }
}

 /**
 * Get the path to a versioned Mix file.
 *
 * @param  string $file
 *
 * @return string
 */
if(!function_exists('mix_cdn'))
{
    function mix_cdn($file)
    {
        return cdn(mix($file));
    }
}
