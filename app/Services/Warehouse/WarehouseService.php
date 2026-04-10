<?php

namespace App\Services\Warehouse;

use App\Models\Warehouse;
use App\Repositories\Warehouse\WarehouseRepositoryInterface;

class WarehouseService
{
    public function __construct(protected WarehouseRepositoryInterface $warehouseRepository)
    {
    }

    /**
     * Get all warehouses with pagination
     */
    public function getAllWarehousesWithPagination($request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->warehouseRepository->getAllWithPagination($perPage);
    }

    /**
     * Get all active warehouses
     */
    public function getAllActiveWarehouses($request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->warehouseRepository->getAllActive($perPage);
    }

    /**
     * Get single warehouse record
     */
    public function getWarehouseById($id)
    {
        return $this->warehouseRepository->find($id);
    }

    /**
     * Create warehouse record
     */
    public function createWarehouse(array $data): Warehouse
    {
        return $this->warehouseRepository->create($data);
    }

    /**
     * Update warehouse record
     */
    public function updateWarehouse($id, array $data): bool
    {
        return $this->warehouseRepository->update($id, $data);
    }

    /**
     * Delete warehouse record
     */
    public function deleteWarehouse($id): bool
    {
        return $this->warehouseRepository->delete($id);
    }
}
