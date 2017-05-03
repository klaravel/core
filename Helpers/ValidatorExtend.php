<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Facades\Validator;

class ValidatorExtend
{
	public static function boot() 
	{
		$class = new \ReflectionClass(new ValidatorExtend);
		$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if ($method->getName() != 'boot')
				$method->invoke(new ValidatorExtend);
		}
	}

	public function recaptcha() 
	{
		Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {

            $curl = new Curl;
            $response = json_decode($curl->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'    => config('services.recaptcha.secret'),
                    'response'  => $value,
                    'remoteip'  => request()->ip()
                ]));

            return $response->success;
        });
	}

	public function old_password() 
	{
		Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            return \Hash::check($value, current($parameters));
        });
	}

	public function slug() 
	{
		Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-zA-Z0-9-]+$/', $value);
        });
	}
}
