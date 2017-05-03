<?php

namespace Modules\Core\Helpers\Menu;

use Illuminate\Support\Facades\Request;
use Modules\Core\Helpers\Menu\BaseMenu;

class AdminMenu extends BaseMenu
{
    protected $parents = [];

    public function create($items) {
    	
        $this->items = $items;
        $this->sortItems();

        $this->setParents();

        return $this->render();
    }

    protected function setParents() 
    {
        $parentKey = $this->getUrlParentKey($this->items, $this->current);

        $parents = $this->key_get_parents($parentKey, $this->items);

        if ($parents)
            $this->parents = array_diff( $parents, ['children'] );
    }

    protected function sortItems() 
    {
        uasort($this->items, function($a, $b) {
            if($a['order'] == $b['order']) return 0;
            return ($a['order'] < $b['order'] ? -1 : 1);
        });

        foreach ($this->items as $key => $value) {
        	if (array_key_exists('children', $value) && count($value['children']) > 0) {

        		foreach ($this->items[$key]['children'] as $key2 => $value2) {
                    if (array_key_exists('children', $value2) && count($value2['children']) > 1) {
                        uasort($this->items[$key]['children'][$key2]['children'], function($a, $b) {
                            if($a['order'] == $b['order']) return 0;
                            return ($a['order'] < $b['order'] ? -1 : 1);
                        });
                    }
                }
				
        		uasort($this->items[$key]['children'], function($a, $b) {
                    if($a['order'] == $b['order']) return 0;
                    return ($a['order'] < $b['order'] ? -1 : 1);
                });
        	}
        }
    }

    public function render($items = null, $level = 1)
    {
        $items = $items ?: $this->items;

        $menu = '<ul class="' . (1 === $level ? 'main-menu level-1' : 'level-' . $level) . '"">';

        foreach($items as $key => $item) {

            if (!$this->checkRolePerms2($item)) continue;

            $classes = array('menu__item');
            $classes[] = $this->getActive2($key, $item);

            $has_children = array_key_exists('children', $item);

            if ($has_children) {
                $classes[] = 'parent';
            }

            $menu .= '<li class="' . implode(' ', $classes) . '" data-sort-' . $item['order'] . '>';
            $menu .= $this->createAnchor2($key, $item, $level);
            $menu .= ($has_children) ? $this->render($item['children'], ++$level) : '';
            $menu .= '</li>';
        }

        $menu .= '</ul>';

        return $menu;
    }

    protected function checkRolePerms2($item) 
    {
    	if (array_key_exists('role', $item)) {
    		$rolePermsDetail = explode(',', $item['role']);

    		if (!auth()->user()->hasRole(explode('|', $rolePermsDetail[0]))) {
                return false;
            }
    	}

    	if (array_key_exists('permission', $item)) {
    		$rolePermsDetail = explode(',', $item['permission']);

    		if (!auth()->user()->can(explode('|', $rolePermsDetail[0]))) {
                return false;
            }
    	}

    	if (array_key_exists('ability', $item)) {
        	$rolePermsDetail = explode(',', $item['ability']);

			$validateAll = false;
            if (count($rolePermsDetail) === 3)
                $validateAll = $rolePermsDetail[2];

            if (!auth()->user()->ability(explode('|', $rolePermsDetail[0]), explode('|', $rolePermsDetail[1]), array('validate_all' => $validateAll))) {
                return false;
            }
    	}

        return true;
    }

    protected function getActive2($key, $item)
    {
        $url = $this->getUrl($item);

        if ($this->current === $url)
            return 'active current';

        if(in_array( $key, $this->parents )) {
            return 'active';
        }
    }

    protected function createAnchor2($key, $item, $level)
    {
        $output = '<a class="menu__item" href="' . $this->getUrl($item) . '">';
        $output .= $this->createIcon($item);

        if ($level === 1) {
			$output .= '<span class="menu-item-parent">';
        }

        $output .= $this->getName($key, $item);

		if ($level === 1) {
			$output .= '</span>';
        }

        $output .= '</a>';

        return $output;
    }

    protected function key_get_parents($subject, $array)
    {
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                if (in_array($subject, array_keys($value)))
                    return array($key);
                else {
                    $chain = $this->key_get_parents($subject, $value);

                    if ((!is_null($chain)))
                        return array_merge(array($key), $chain);
                }
            }
        }

        return null;
    }

    protected function getUrlParentKey($array, $needle, $parent = null) 
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $pass = $parent;
                if (is_string($key)) {
                    $pass = $key;
                }
                $found = $this->getUrlParentKey($value, $needle, $pass);
                if ($found !== false) {
                    return $found;
                }
            } else if ($key === 'url' && $value === $needle) {
                return $parent;
            }
        }

        return false;
    }
}
