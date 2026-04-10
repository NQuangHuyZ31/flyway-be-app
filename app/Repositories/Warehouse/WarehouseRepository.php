<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse;
use App\Repositories\BaseRepository;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    public function getModel()
    {
        return Warehouse::class;
    }

    public function getAllWithPagination($perPage)
    {
        return Warehouse::with('sections')
            ->paginate($perPage ?? 20);
    }

    public function getAllActive($perPage)
    {
        return Warehouse::with('sections')
            ->active()
            ->paginate($perPage ?? 20);
    }
}
