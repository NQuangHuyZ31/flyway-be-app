<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use App\Repositories\Inventory\InventoryRepositoryInterface;

class InventoryService
{
    public function __construct(protected InventoryRepositoryInterface $inventoryRepository)
    {
    }

    /**
     * Get all inventory with pagination
     */
    public function getAllInventoryWithPagination($request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->inventoryRepository->getAllWithPagination($perPage);
    }

    /**
     * Get inventory by warehouse
     */
    public function getInventoryByWarehouse($warehouseId, $request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->inventoryRepository->getByWarehouse($warehouseId, $perPage);
    }

    /**
     * Get inventory by product
     */
    public function getInventoryByProduct($productId, $request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->inventoryRepository->getByProduct($productId, $perPage);
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems($request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->inventoryRepository->getLowStock($perPage);
    }

    /**
     * Get single inventory record
     */
    public function getInventoryById($id)
    {
        return $this->inventoryRepository->find($id);
    }

    /**
     * Create inventory record
     */
    public function createInventory(array $data): Inventory
    {
        return $this->inventoryRepository->create($data);
    }

    /**
     * Update inventory record
     */
    public function updateInventory($id, array $data): bool
    {
        return $this->inventoryRepository->update($id, $data);
    }

    /**
     * Delete inventory record
     */
    public function deleteInventory($id): bool
    {
        return $this->inventoryRepository->delete($id);
    }

    /**
     * Adjust inventory quantity
     */
    public function adjustInventory($id, array $data): bool
    {
        $inventory = $this->inventoryRepository->find($id);
        
        if (!$inventory) {
            return false;
        }

        $updateData = [];
        
        if (isset($data['quantity_on_hand'])) {
            $updateData['quantity_on_hand'] = $data['quantity_on_hand'];
        }
        
        if (isset($data['quantity_reserved'])) {
            $updateData['quantity_reserved'] = $data['quantity_reserved'];
        }

        // Calculate available quantity
        if (isset($updateData['quantity_on_hand']) || isset($updateData['quantity_reserved'])) {
            $qtyOnHand = $updateData['quantity_on_hand'] ?? $inventory->quantity_on_hand;
            $qtyReserved = $updateData['quantity_reserved'] ?? $inventory->quantity_reserved;
            $updateData['quantity_available'] = max(0, $qtyOnHand - $qtyReserved);
        }

        $updateData['last_transaction_at'] = now();
        
        return $this->inventoryRepository->update($id, $updateData);
    }
}
