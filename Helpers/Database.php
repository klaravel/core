<?php

namespace Modules\Core\Helpers;

class Database
{
	/**
	 * Set sequence numbers store in tabe's row.
	 * 
	 * @param  [type] $table              [description]
	 * @param  [type] $sequenceColumnName [description]
	 * @param  [type] $sequenceIds        [description]
	 * @param  string $where              [description]
	 * @return [type]                     [description]
	 */
	public static function sequence($table, $sequenceColumnName, $sequenceIds, $where = '1=1')
	{
		$query = "UPDATE {$table} SET {$sequenceColumnName} = CASE ID ";

		foreach($sequenceIds as $key => $value){
			$query .= " WHEN " . $value . " THEN " . ($key + 1);
		}

		$query .= " END WHERE id IN (" . join(',', $sequenceIds) . ") AND {$where}";

		return \DB::statement($query);
	}
}
