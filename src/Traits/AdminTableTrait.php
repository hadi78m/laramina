<?php

namespace AdminPlatform\Traits;

use Illuminate\Http\Request;

trait AdminTableTrait
{

    public static function adminTable(Request $request, array $config = [], ?callable $transformCallback = null)

    {

        $model = new static;
        $query = $model->newQuery();

        $perPage = $request->get('per_page', 15);
        $searchColumns = $config['search'] ?? [];
        $filters = $config['filters'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search') && count($searchColumns)) {
            $search = $request->search;
            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $col) {
                    $q->orWhere($col, 'LIKE', "%{$search}%");
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */


        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->$filter);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SORT - تطابق با فرمت DataTable
        |--------------------------------------------------------------------------
        */


        if ($request->filled('sort')) {
            $sortField = $request->sort;
            $direction = $request->get('direction', 'asc');
            $query->orderBy($sortField, $direction);
        } else {
            $query->orderByDesc('id');
        }

        $rows = $query->paginate($perPage);
        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $rows = $query->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | TRANSFORM
        |--------------------------------------------------------------------------
        */

        $data = $rows->items();

        if ($transformCallback && is_callable($transformCallback)) {
            $data = collect($data)->map($transformCallback);
        } elseif (method_exists(static::class, 'adminTransform')) {
            $data = collect($data)->map(fn($row) => static::adminTransform($row));
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        // ✅ اصلاح ساختار پاسخ - مطابق با expectation table.js

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $rows->total(),
            'per_page' => $rows->perPage(),
            'current_page' => $rows->currentPage(),
            'last_page' => $rows->lastPage(),
            'from' => $rows->firstItem(),
            'to' => $rows->lastItem(),
        ]);

        return response()->json([

            'success' => true,

            'data' => $data,

            'meta' => [

                'current_page' => $rows->currentPage(),

                'per_page' => $rows->perPage(),

                'total' => $rows->total(),

            ]

        ]);
    }
}
