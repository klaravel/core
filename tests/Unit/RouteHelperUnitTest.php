<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Core\Testing\DatabaseMigrations;
use Tests\TestCase;

class RouteHelperUnitTest extends TestCase
{
	/** @test */
	public function index_method_should_create_proper_route()
	{
	    RouteHelper::index('dummies', 'DummiesController');

	    $route = $this->getRoute('dummies');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.index', $route->getName());
		$this->assertEquals('DummiesController@index', $route->getActionName());

		RouteHelper::index('dummies1', 'DummiesController', 'dummies.index');
		$route = $this->getRoute('dummies1');

		$this->assertContains('ability:superadmin,dummies.index', $route->middleware());
	}

	/** @test */
	public function list_method_should_create_proper_route()
	{
	    RouteHelper::list('dummies', 'DummiesController');

	    $route = $this->getRoute('dummies', 'POST');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.list', $route->getName());
		$this->assertEquals('DummiesController@list', $route->getActionName());
		$this->assertContains('POST', $route->methods());

		RouteHelper::list('dummies1', 'DummiesController', 'dummies.list');
		$route = $this->getRoute('dummies1', 'POST');

		$this->assertContains('ability:superadmin,dummies.list', $route->middleware());
		$this->assertContains('ajax', $route->middleware());
	}

	/** @test */
	public function create_method_should_create_proper_route()
	{
	    RouteHelper::create('dummies', 'DummiesController');

	    $route = $this->getRoute('dummies/create');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.create', $route->getName());
		$this->assertEquals('DummiesController@create', $route->getActionName());

		RouteHelper::create('dummies1', 'DummiesController', 'dummies.create');
		$route = $this->getRoute('dummies1/create');

		$this->assertContains('ability:superadmin,dummies.create', $route->middleware());
	}

	/** @test */
	public function store_method_should_create_proper_route()
	{
	    RouteHelper::store('dummies', 'DummiesController');

	    $route = $this->getRoute('dummies/store', 'POST');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.store', $route->getName());
		$this->assertEquals('DummiesController@store', $route->getActionName());
		$this->assertContains('POST', $route->methods());

		RouteHelper::store('dummies1', 'DummiesController', 'dummies.store');
		$route = $this->getRoute('dummies1/store', 'POST');

		$this->assertContains('ability:superadmin,dummies.store', $route->middleware());
		$this->assertContains('ajax', $route->middleware());
	}

	/** @test */
	public function edit_method_should_create_proper_route()
	{
	    RouteHelper::edit('dummies', 'dummy', 'DummiesController');

	    $route = $this->getRoute('dummies/{dummy}');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.edit', $route->getName());
		$this->assertEquals('DummiesController@edit', $route->getActionName());

		RouteHelper::edit('dummies1', 'dummy', 'DummiesController', 'dummies.edit');
		$route = $this->getRoute('dummies1/{dummy}');

		$this->assertContains('ability:superadmin,dummies.edit', $route->middleware());
	}

	/** @test */
	public function update_method_should_create_proper_route()
	{
	    RouteHelper::update('dummies', 'dummy', 'DummiesController');

	    $route = $this->getRoute('dummies/{dummy}', 'PATCH');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.update', $route->getName());
		$this->assertEquals('DummiesController@update', $route->getActionName());

		RouteHelper::update('dummies1', 'dummy', 'DummiesController', 'dummies.update');
		$route = $this->getRoute('dummies1/{dummy}', 'PATCH');

		$this->assertContains('ability:superadmin,dummies.update', $route->middleware());
		$this->assertContains('ajax', $route->middleware());
	}

	/** @test */
	public function sequence_method_should_create_proper_route()
	{
	    RouteHelper::sequence('dummies', 'DummiesController', 'dummies.sequence');

	    $route = $this->getRoute('dummies/sequence');

    	$this->assertNotNull($route);
		$this->assertEquals('dummies.sequence', $route->getName());
		$this->assertEquals('DummiesController@sequence', $route->getActionName());
		$this->assertContains('ability:superadmin,dummies.sequence', $route->middleware());


		$route = $this->getRoute('dummies/sequence', 'POST');

    	$this->assertNotNull($route);
    	$this->assertEquals('dummies.update-sequence', $route->getName());
		$this->assertEquals('DummiesController@updateSequence', $route->getActionName());
		$this->assertContains('ability:superadmin,dummies.sequence', $route->middleware());
		$this->assertContains('ajax', $route->middleware());
	}

	private function getRoute($url, $method = 'GET')
	{
	    $routes = Route::getRoutes();

	    foreach ($routes as $route) {
	    	if ($route->uri() == $url && in_array($method, $route->methods()))
	    		return $route;
	    }

	    return null;
	}
}
