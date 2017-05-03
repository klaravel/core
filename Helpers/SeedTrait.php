<?php

namespace Modules\Core\Helpers;

use Modules\Admin\Http\AdminPermission;

trait SeedTrait
{
	public function permsInsert($name, $displayName, $parentId = null, $basicOperations = false) 
	{
		$perms = AdminPermission::firstOrCreate([
                'name' => $name,
                'display_name' => $displayName,
                'parent_id' => $parentId
            ]);

		if ($basicOperations)
			$this->permsBasicOperations($name, $perms->id);

		return $perms;
	}

	public function permsBasicOperations($namePrefix, $parentId) 
    {
    	$this->permsList($namePrefix, $parentId);
    	$this->permsCreate($namePrefix, $parentId);
    	$this->permsEdit($namePrefix, $parentId);
    	$this->permsDelete($namePrefix, $parentId);
    }

    public function permsList($namePrefix, $parentId) 
    {
        $this->permsInsert($namePrefix . '.list', 'List', $parentId);
    }

    public function permsCreate($namePrefix, $parentId) 
    {
    	$this->permsInsert($namePrefix . '.create', 'Create', $parentId);
    }

    public function permsEdit($namePrefix, $parentId) 
    {
    	$this->permsInsert($namePrefix . '.edit', 'Edit', $parentId);
    }

    public function permsDelete($namePrefix, $parentId) 
    {
    	$this->permsInsert($namePrefix . '.delete', 'Delete', $parentId);
    }

    public function permsSequence($namePrefix, $parentId) 
    {
    	$this->permsInsert($namePrefix . '.sequence', 'Sequence', $parentId);
    }
}
