<?php

namespace App\Services\StockOutputVoucher;

use App\Models\StockOutputVoucher;
use App\Repositories\StockOutputVoucher\StockOutputVoucherRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StockOutputVoucherService
{
    public function __construct(protected StockOutputVoucherRepositoryInterface $repository)
    {
    }

    /**
     * Get all vouchers with pagination
     */
    public function getAllVouchersWithPagination($request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->repository->getAllWithPagination($perPage);
    }

    /**
     * Get by warehouse
     */
    public function getVouchersByWarehouse($warehouseId, $request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->repository->getByWarehouse($warehouseId, $perPage);
    }

    /**
     * Get by status
     */
    public function getVouchersByStatus($statusId, $request)
    {
        $perPage = $request->input('per_page') ?? 20;
        return $this->repository->getByStatus($statusId, $perPage);
    }

    /**
     * Get single voucher
     */
    public function getVoucherById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create new voucher (Draft status)
     */
    public function createVoucher(array $data): StockOutputVoucher
    {
        return DB::transaction(function () use ($data) {
            $voucher = $this->repository->create($data);
            
            // Add line items if provided
            if (!empty($data['items'])) {
                $lineNumber = 1;
                foreach ($data['items'] as $item) {
                    $item['stock_out_id'] = $voucher->id;
                    $item['line_number'] = $lineNumber;
                    // Calculate line total
                    $item['line_total'] = ($item['quantity_ordered'] ?? 0) * ($item['unit_cost'] ?? 0);
                    
                    $voucher->items()->create($item);
                    $lineNumber++;
                }
            }

            return $voucher;
        });
    }

    /**
     * Update voucher (only if still draft)
     */
    public function updateVoucher($id, array $data): bool
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            return false;
        }

        return DB::transaction(function () use ($id, $data, $voucher) {
            // Update main record
            $this->repository->update($id, $data);

            // Update items if provided
            if (!empty($data['items'])) {
                $voucher->items()->delete();
                $lineNumber = 1;
                foreach ($data['items'] as $item) {
                    $item['stock_out_id'] = $voucher->id;
                    $item['line_number'] = $lineNumber;
                    $item['line_total'] = ($item['quantity_ordered'] ?? 0) * ($item['unit_cost'] ?? 0);
                    
                    $voucher->items()->create($item);
                    $lineNumber++;
                }
            }

            return true;
        });
    }

    /**
     * Delete voucher
     */
    public function deleteVoucher($id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Submit voucher for approval
     */
    public function submitVoucher($id): StockOutputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        if ($voucher->items()->count() === 0) {
            throw new \Exception('Voucher must have at least one item');
        }

        // TODO: Update status to pending when VoucherStatus table is fully integrated
        return $voucher;
    }

    /**
     * Approve voucher - checks stock availability using FIFO
     */
    public function approveVoucher($id, $userId): StockOutputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        // TODO: Check current status is "pending"
        // TODO: Check user permission
        // TODO: Validate stock availability for all items using FIFO

        $updateData = [
            'approved_by' => $userId,
            'approved_at' => now(),
        ];

        $this->repository->update($id, $updateData);
        
        return $this->repository->find($id);
    }

    /**
     * Complete voucher - deducts from inventory
     * Uses FIFO (First In, First Out) for batch selection
     */
    public function completeVoucher($id, $userId): StockOutputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        return DB::transaction(function () use ($voucher, $userId) {
            foreach ($voucher->items as $item) {
                // TODO: Select batches using FIFO
                // TODO: Deduct from inventory
                // TODO: Create inventory transaction record
                
                $item->update([
                    'quantity_shipped' => $item->quantity_ordered,
                ]);
            }

            // Mark voucher as completed
            $voucher->update([
                'completed_by' => $userId,
                'completed_at' => now(),
                // TODO: Update status to "completed"
            ]);

            return $this->repository->find($voucher->id);
        });
    }

    /**
     * Cancel voucher item
     */
    public function cancelItem($voucherId, $itemId, string $reason): void
    {
        $voucher = $this->repository->find($voucherId);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        $item = $voucher->items()->find($itemId);
        
        if (!$item) {
            throw new \Exception('Item not found');
        }

        $item->update([
            'quantity_cancelled' => $item->quantity_ordered,
            'cancellation_notes' => $reason,
        ]);
    }

    /**
     * Calculate total cost for voucher
     */
    public function calculateVoucherTotal($id): float
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            return 0;
        }

        return $voucher->items()->sum('line_total');
    }

    /**
     * Reject voucher
     */
    public function rejectVoucher($id, string $reason): StockOutputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        // TODO: Update status to "rejected"
        $this->repository->update($id, [
            'rejection_reason' => $reason,
        ]);

        return $this->repository->find($id);
    }
}
