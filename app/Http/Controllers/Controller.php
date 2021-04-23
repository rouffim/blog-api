<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    const SORT_TYPE_ASC = 'asc';
    const SORT_TYPE_DESC = 'desc';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param $model
     * @param string $searchColumnName
     * @param int $perPageDefault
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function pageableRequest(Request $request, $model, string $searchColumnName, int $perPageDefault = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $model::query();

        $perPageParam = $request->query('perPage');
        $perPage = Str::length($perPageParam) > 0 && is_numeric($perPageParam) ? $perPageParam : $perPageDefault;
        $sort = $request->query('sort');
        $sortType = $request->query('sortType');
        $sortType = isset($sortType) && ($sortType === self::SORT_TYPE_ASC || $sortType === self::SORT_TYPE_DESC) ?
            $sortType : self::SORT_TYPE_ASC;
        $search = $request->query('search');

        if(Str::length($search) > 0) {
            $query->where($searchColumnName, 'like', "%$search%");
        }

        if(Str::length($sort) > 0 && in_array($sort, $model::$sortable)) {
            $query = $query->orderBy($sort, $sortType);
        }

        return $query->paginate($perPage);
    }
}
