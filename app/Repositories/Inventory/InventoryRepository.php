<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
use App\Repositories\BaseRepository;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    public function getModel()
    {
        return Inventory::class;
    }

    public function getAllWithPagination($perPage)
    {
        return Inventory::with(['product', 'warehouse', 'section'])
            ->paginate($perPage ?? 20);
    }

    public function getByWarehouse($warehouseId, $perPage)
    {
        return Inventory::with(['product', 'warehouse', 'section'])
            ->byWarehouse($warehouseId)
            ->paginate($perPage ?? 20);
    }

    public function getByProduct($productId, $perPage)
    {
        return Inventory::with(['product', 'warehouse', 'section'])
            ->byProduct($productId)
            ->paginate($perPage ?? 20);
    }

    public function getLowStock($perPage)
    {
        return Inventory::with(['product', 'warehouse', 'section'])
            ->lowStock()
            ->paginate($perPage ?? 20);
    }
}
