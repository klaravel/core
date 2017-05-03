<?php

namespace Modules\Core\Helpers\Menu;

use Illuminate\Support\Facades\Request;

abstract class BaseMenu
{
	protected $items = [];
    protected $current;
    protected $currentKey;

    public function __construct() {
        $this->current = str_replace(config('app.url'), '', Request::url());
    }

    /*
     * Shortcut method for create a menu with a callback.
     * This will allow you to do things like fire an even on creation.
     * 
     * @param callable $callback Callback to use after the menu creation
     * @return object
     */
    public function create($callback) {
        $callback($this->items);
        $this->sortItems();

        return $this;
    }

	/*
     * Add a menu item to the item stack
     * 
     * @param string $key Dot separated hierarchy
     * @param string $name Text for the anchor
     * @param string $url URL for the anchor
     * @param integer $sort Sorting index for the items
     * @param string $icon URL to use for the icon
     */
	public function add($key, $name, $url, $sort = 0, $icon = null, $rolePerms = null)
    {
        if ($this->in_array_r($key, $this->items)) {
            return;
        }

        $item = array(
            'key'       => $key,
            'name'      => $name,
            'url'       => $url,
            'sort'      => $sort,
            'icon'      => $icon,
            'rolePerms' => $rolePerms,
            'children'  => array()
        );

        $children = str_replace('.', '.children.', $key);
        array_set($this->items, $children, $item);

        if($url == $this->current) {
            $this->currentKey = $key;
        }
    }

    protected function in_array_r($needle, $haystack, $strict = false) 
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    /*
     * Recursive function to loop through items and create a menu
     * 
     * @param array $items List of items that need to be rendered
     * @param boolean $level Which level you are currently rendering
     * @return string
     */
    public function render($items = null, $level = 1)
    {
        $items = $items ?: $this->items;

        $menu = '<ul class="' . (1 === $level ? 'main-menu level-1' : 'level-' . $level) . '"">';

        foreach($items as $item) {

            if (!$this->checkRolePerms($item['rolePerms'])) continue;

            $classes = array('menu__item');
            $classes[] = $this->getActive($item);

            $has_children = sizeof($item['children']);

            if ($has_children) {
                $classes[] = 'parent';
            }

            $menu .= '<li class="' . implode(' ', $classes) . '" data-sort-' . $item['sort'] . '>';
            $menu .= $this->createAnchor($item, $level);
            $menu .= ($has_children) ? $this->render($item['children'], ++$level) : '';
            $menu .= '</li>';
        }

        $menu .= '</ul>';

        return $menu;
    }

    protected function checkRolePerms($item) 
    {
        if (!$item) return true;

        $rolePerms = explode(':', $item);
        $rolePermsDetail = explode(',', $rolePerms[1]);

        switch ($rolePerms[0]) {
            case 'role':
                if (!auth()->user()->hasRole(explode('|', $rolePermsDetail[0]))) {
                    return false;
                }
                break;
            case 'permission':
                if (!auth()->user()->can(explode('|', $rolePermsDetail[0]))) {
                    return false;
                }
                break;
            case 'ability':
                $validateAll = false;
                if (count($rolePermsDetail) === 3)
                    $validateAll = $rolePermsDetail[2];

                if (!auth()->user()->ability(explode('|', $rolePermsDetail[0]), explode('|', $rolePermsDetail[1]), array('validate_all' => $validateAll))) {
                    return false;
                }
                break;
        }

        return true;
    }

    /*
     * Method to render an anchor
     * 
     * @param array $item Item that needs to be turned into a link
     * @return string
     */
    protected function createAnchor($item, $level)
    {
    	// <a href="{{ route('admin.home') }}"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Dashboard</span></a>
        $output = '<a class="menu__item" href="' . $item['url'] . '">';
        $output .= $this->createIcon($item);

        if ($level === 1) {
			$output .= '<span class="menu-item-parent">';
        }

        $output .= $item['name'];

		if ($level === 1) {
			$output .= '</span>';
        }

        $output .= '</a>';

        return $output;
    }

    /*
     * Method to render an icon
     * 
     * @param array $item Item that needs to be turned into a icon
     * @return string
     */
    protected function createIcon($item)
    {
        $output = '';

        if (array_key_exists('icon', $item)) {
            $output .= sprintf(
                '<i class="fa fa-lg fa-fw fa-%s"></i> ',
                $item['icon']
            );
        }

        return $output;
    }

    /*
     * Method to sort through the menu items and put them in order
     * 
     * @return void
     */
    protected function sortItems() {

        usort($this->items, function($a, $b) {
            if($a['sort'] == $b['sort']) return 0;
            return ($a['sort'] < $b['sort'] ? -1 : 1);
        });

        foreach ($this->items as $key => $value) {
            if (count($value['children']) > 0) {
                foreach ($this->items[$key]['children'] as $key2 => $value2) {
                    if (count($value2['children']) > 1) {
                        usort($this->items[$key]['children'][$key2]['children'], function($a, $b) {
                            if($a['sort'] == $b['sort']) return 0;
                            return ($a['sort'] < $b['sort'] ? -1 : 1);
                        });
                    }
                }

                usort($this->items[$key]['children'], function($a, $b) {
                    if($a['sort'] == $b['sort']) return 0;
                    return ($a['sort'] < $b['sort'] ? -1 : 1);
                });
            }
        }
    }

    /*
     * Method to find the active links
     * 
     * @param array $item Item that needs to be checked if active
     * @return string
     */
    protected function getActive($item)
    {
        $url = trim($item['url'], '/');

        if ($this->current === $url)
        {
            return 'active current';
        }

        if(strpos($this->currentKey, $item['key']) === 0) {
            return 'active';
        }
    }

    protected function getName($key, $item) 
    {
        if (array_key_exists('name', $item))
            return $item['name'];

        return title_case(str_replace(['-', '_'], ' ', $key));
    }

    protected function getUrl($item) 
    {
        if (array_key_exists('url', $item))
            return $item['url'];
        else
            return 'javascript:;';
    }
}
