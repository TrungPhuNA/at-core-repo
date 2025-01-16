<?php

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Created By PhpStorm
 * Code By : trungphuna
 * Date: 1/16/25
 */
class ResponseHelper
{
    /**
     * Get LengthAwarePaginator
     *
     * @param  LengthAwarePaginator  $paginator
     * @param  boolean  $convertTime
     * @return array
     */
    public static function getLengthAwarePaginatorData(LengthAwarePaginator $paginator): array
    {
        $collections = $paginator->getCollection();
        $meta = [
            "total"      => (int) $paginator->total(),
            "total_page" => (int) $paginator->lastPage(),
            "page"       => (int) $paginator->currentPage(),
            "page_size"  => (int) $paginator->perPage(),
        ];

        return [$collections, $meta];
    }
}