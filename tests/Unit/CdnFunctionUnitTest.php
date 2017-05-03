<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Core\Testing\DatabaseMigrations;
use Tests\TestCase;

class CdnFunctionUnitTest extends TestCase
{
	/** @test */
	public function cdn_function_return_cdn_path() 
	{
		$path = cdn('/css/style.css');

		$expectedPath = config('core.cdn_url') . '/css/style.css';

		$this->assertEquals($expectedPath, $path);
	}
}
