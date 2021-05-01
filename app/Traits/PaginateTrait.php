<?php

namespace App\Traits;

trait PaginateTrait
{
    public function getValidPerpage($perpage)
    {
        if (!in_array($perpage, [10, 20, 50, 100, 500, 1000])) {
            $perpage = 20;
        }
        return $perpage;
    }

    public function getValidOrderDirection($orderDirection, $orderDirectionDefault = 'desc')
    {
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = $orderDirectionDefault;
        }
        return $orderDirection;
    }

    public function getValidParams($request)
    {
        return $request->except(['order', 'perpage', 'order_direction', 'status_sampel', 'page', 'search', 'params']);
    }
}
