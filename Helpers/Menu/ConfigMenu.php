<?php

namespace Modules\Core\Helpers\Menu;

use Illuminate\Support\Facades\Request;
use Modules\Core\Helpers\Menu\BaseMenu;

class ConfigMenu extends BaseMenu
{
    public function render($items = null, $level = 1)
    {
        $items = $items ?: $this->items;

        $menu = '<ul class="' . (1 === $level ? 'list-unstyled left-list main-menu level-1' : 'level-' . $level) . '"">';

        foreach($items as $item) {

            if (!$this->checkRolePerms($item['rolePerms'])) continue;

            $classes = array('menu__item');
            $classes[] = $this->getActive($item);

            $has_children = sizeof($item['children']);

            if ($has_children) {
                $classes[] = 'parent';
            }

            $menu .= '<li class="' . implode(' ', $classes) . '" data-sort-' . $item['sort'] . ' data-level'.$level.'>';
            if ($has_children) {
            	$menu .= '<h6>'.$item['name'].'</h6>';
            } else {
            	$menu .= $this->createAnchor($item, $level);
            }
            $menu .= ($has_children) ? $this->render($item['children'], ++$level) : '';
            $menu .= '</li>';
        }

        $menu .= '</ul>';

        return $menu;
    }

    protected function getActive($item)
    {
        $url = trim($item['url'], '/');

        if ($this->current === $url && $this->current != route('admin.config'))
        {
            return 'active current';
        }
    }
}
