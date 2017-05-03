<?php

namespace Modules\Core\Filters;

use Modules\Core\Filters\QueryFilter;

trait QueryFilterTrait
{
    public function scopeFilters($query, QueryFilter $filters) 
    {
    	return $filters->apply($query);
    }
}
