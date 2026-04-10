<?php

namespace App\Repositories\Inventory;

use App\Repositories\RepositoryInterface;

interface InventoryRepositoryInterface extends RepositoryInterface
{
    public function getAllWithPagination($perPage);
    public function getByWarehouse($warehouseId, $perPage);
    public function getByProduct($productId, $perPage);
    public function getLowStock($perPage);
}
