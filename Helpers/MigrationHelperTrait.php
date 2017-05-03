<?php

namespace Modules\Core\Helpers;

trait MigrationHelperTrait
{
	/**
	 * Basic scheduling fields for schedule record.
	 * 
	 * @param  Illuminate\Database\Schema\Blueprint $table
	 * @param  string $default
	 * @return void
	 */
	public function schedule($table, $default = 'active') 
	{
        // active, inactive, schedule
        $table->string('status')->default($default);

        // scheduled date range
        $table->dateTime('start_at')->nullable();
        $table->dateTime('end_at')->nullable();
	}
}
