<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Facades\Blade;

class BladeDirective
{
	public static function boot() 
	{
		$class = new \ReflectionClass(new BladeDirective);
		$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if ($method->getName() != 'boot')
				$method->invoke(new BladeDirective);
		}
	}

	public function formStartEnd() 
	{
        Blade::directive('formStart', function ($expression) {
            return 
            '<section class="well">
                <form class="form-horizontal" data-ajax="true"
                    action="{{ '.$expression.' }}" method="POST">
                    {{ csrf_field() }}';
        });

        Blade::directive('formEnd', function(){
            return 
                '</form>
            </section>';
        });
    }

    public function buttons() 
    {
        Blade::directive('btnBackToList', function ($expression) {
            return 
            '<a class="btn btn-primary" href="{{ '.$expression.' }}">
                <i class="fa fa-long-arrow-left"></i> Back to List</a>';
        });

        Blade::directive('btnExport', function ($expression) {
            return 
            '<a class="btn btn-primary" href="{{ '.$expression.' }}">
                <i class="fa fa-file-excel-o"></i> Export</a>';
        });

        Blade::directive('btnChangeSequence', function ($expression) {
            return 
            '<a class="btn btn-primary bg-color-teal" href="{{ '.$expression.' }}">
                <i class="fa fa-sort"></i> Change Sequence</a>';
        });

        Blade::directive('btnAdd', function ($expression) {
            return 
            '<a class="btn btn-primary" href="{{ '.explode(',', $expression)[0].' }}">
                <i class="fa fa-plus"></i> Add '.trim(explode(',', $expression)[1], ' \'\"').'</a>';
        });

        Blade::directive('btnEdit', function ($expression) {
            return
            '<a href="{{ '.$expression.' }}/@{{ id }}" 
                rel="tooltip" data-original-title="Edit" data-placement="bottom" class="mglr-2">
                <i class="glyphicon glyphicon-edit"></i></a>';
        });

        Blade::directive('btnDelete', function ($expression) {
            return
            '<a href="javascript:;" class="text-danger" data-delete="true" data-id="@{{ id }}"
                rel="tooltip" data-original-title="Delete" data-placement="bottom" class="mglr-2">
                <i class="glyphicon glyphicon-trash"></i></a>';
        });
	}

	public function tableRows() 
	{
        Blade::directive('thSearchDate', function ($expression) {
            return 
            '<th class="hasinput" rowspan="1" colspan="1">
                <div class="input-group mgb-5">
                    <input type="text" name="from'.trim($expression, ' \'\"').'" placeholder="From date" 
                        class="form-control datepicker" data-dateformat="mm/dd/yy">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                <div class="input-group">
                    <input type="text" name="to'.trim($expression, ' \'\"').'" placeholder="To date" 
                        class="form-control datepicker" data-dateformat="mm/dd/yy">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </th>';
        });

        Blade::directive('trNoRecords', function ($expression) {
            return
            '<tr>
                <td class="text-center" colspan="'.$expression.'">No records</td>
            </tr>';
        });
	}
}
