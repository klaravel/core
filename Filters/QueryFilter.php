<?php

namespace Modules\Core\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
	protected $request;
	protected $builder;

	public function __construct(Request $request) 
	{
		$this->request = $request;
	}

	public function apply(Builder $builder) 
	{
		$this->builder = $builder;

		foreach ($this->filters() as $name => $value) {
			if (!empty($value) && method_exists($this, $name)){
				call_user_func_array([$this, $name], array_filter([$value]));
			}

			// extra status for check because empty('0') is 
			//  true on before condition
			if ($name == 'status' && $value == '0' && method_exists($this, 'status')) {
				$this->status(false);
			}
		}

		return $this->builder;
	}

	public function filters() 
	{
		return $this->request->all();
	}
}
