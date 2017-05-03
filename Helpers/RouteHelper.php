<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
	public function resource($name, $model, $controller, $perms = null)
	{
		$this->indexAndList($name, $controller, $perms);
		$this->createAndStore($name, $controller, $perms);
		$this->editAndUpdate($name, $model, $controller, $perms);
	}

	public function indexAndList($name, $controller, $perms = null) 
	{
		$this->index($name, $controller, $perms);
		$this->list($name, $controller, $perms);
	}	

	public function createAndStore($name, $controller, $perms = null) 
	{
		$this->create($name, $controller, $perms);
		$this->store($name, $controller, $perms);
	}

	public function editAndUpdate($name, $model, $controller, $perms = null) 
	{
		$this->edit($name, $model, $controller, $perms);
		$this->update($name, $model, $controller, $perms);
	}

	public function index($name, $controller, $perms = null) 
	{
		Route::get($name, $controller . '@index')
				->name($this->nameParser($name) . '.index')
				->middleware($this->middleware($perms));
	}

	public function list($name, $controller, $perms = null) 
	{
		Route::post($name, $controller . '@list')
				->name($this->nameParser($name) . '.list')
				->middleware($this->middleware($perms, ['ajax']));
	}

	public function create($name, $controller, $perms = null) 
	{
		Route::get($name . '/create', $controller . '@create')
				->name($this->nameParser($name) . '.create')
				->middleware($this->middleware($perms));
	}

	public function store($name, $controller, $perms = null) 
	{
		Route::post($name . '/store', $controller . '@store')
				->name($this->nameParser($name) . '.store')
				->middleware($this->middleware($perms, ['ajax']));
	}

	public function edit($name, $model, $controller, $perms = null) 
	{
		Route::get($name . '/{'.$model.'}', $controller . '@edit')
				->name($this->nameParser($name) . '.edit')
				->middleware($this->middleware($perms))
				->where($model, '[0-9]+');
	}

	public function update($name, $model, $controller, $perms = null) 
	{
		Route::patch($name . '/{'.$model.'}', $controller . '@update')
				->name($this->nameParser($name) . '.update')
				->middleware($this->middleware($perms, ['ajax']))
				->where($model, '[0-9]+');
	}

	public function sequence($name, $controller, $perms = null) 
	{
		Route::get($name . '/sequence', $controller . '@sequence')
				->name($this->nameParser($name) . '.sequence')
				->middleware($this->middleware($perms));
		
		Route::post($name . '/sequence', $controller . '@updateSequence')
				->name($this->nameParser($name) . '.update-sequence')
				->middleware($this->middleware($perms, ['ajax']));
	}

	private function middleware($perms, $middleware = [])
	{
		if ($perms != null)
			$middleware = array_merge($middleware, ['ability:superadmin,'.$perms]);

		return $middleware;
	}

	private function nameParser($name) 
	{
		return str_replace('/', '.', $name);
	}
}
