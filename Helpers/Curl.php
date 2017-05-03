<?php

namespace Modules\Core\Helpers;

class Curl
{
	/**
	 * Curl post data and get response from url.
	 * 
	 * @param  string $url
	 * @param  string $payload
	 * @return string
	 */
	public function post($url, $payload) 
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		curl_close($ch); 

		return $response;
	}
}
