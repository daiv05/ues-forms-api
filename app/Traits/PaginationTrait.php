<?php

namespace App\Traits;

trait PaginationTrait
{
    /**
     * Paginate the given data.
     *
     * @param array $data
     * @param int $perPage
     * @param int $currentPage
     * @return array
     */
    public function paginate(array $data, int $perPage, int $currentPage = 1): array
    {
        $totalItems = count($data);
        $totalPages = (int) ceil($totalItems / $perPage);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedData = array_slice($data, $offset, $perPage);

        return [
            'data' => $paginatedData,
            'pagination' => [
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalItems),
                'per_page' => $perPage,
                'page' => $currentPage,
                'nextPage' => $currentPage < $totalPages ? $currentPage + 1 : null,
                'previousPage' => $currentPage > 1 ? $currentPage - 1 : null,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
        ];
    }
}