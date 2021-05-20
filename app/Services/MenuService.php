<?php

namespace App\Services;

class MenuService
{

    public function arrangeMenuItems($elements, $parentId = 0)
    {
        $menuHierarchy = array();

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $element->children = [];
                $children = $this->arrangeMenuItems($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $menuHierarchy[] = $element;
            }
        }

        return $menuHierarchy;
    }

}
