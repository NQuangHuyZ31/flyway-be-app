<?php

namespace App\Services\StockInputVoucher;

use App\Models\StockInputVoucher;
use App\Repositories\StockInputVoucher\StockInputVoucherRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StockInputVoucherService
{
    public function __construct(protected StockInputVoucherRepositoryInterface $repository)
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
    public function createVoucher(array $data): StockInputVoucher
    {
        return DB::transaction(function () use ($data) {
            $voucher = $this->repository->create($data);
            
            // Add line items if provided
            if (!empty($data['items'])) {
                $lineNumber = 1;
                foreach ($data['items'] as $item) {
                    $item['stock_in_id'] = $voucher->id;
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
                    $item['stock_in_id'] = $voucher->id;
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
     * Submit voucher for approval (moves from draft → pending)
     * Note: Status management should be handled in future 
     */
    public function submitVoucher($id): StockInputVoucher
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
     * Approve voucher (as manager)
     * Note: Status management should be handled in future
     */
    public function approveVoucher($id, $userId): StockInputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        // TODO: Check current status is "pending"
        // TODO: Check user permission

        $updateData = [
            'approved_by' => $userId,
            'approved_at' => now(),
        ];

        $this->repository->update($id, $updateData);
        
        return $this->repository->find($id);
    }

    /**
     * Receive voucher items (staff marks items as received)
     * TODO: Check available stock and update inventory
     */
    public function receiveVoucher($id, $userId, array $receivedItems = []): StockInputVoucher
    {
        $voucher = $this->repository->find($id);
        
        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        return DB::transaction(function () use ($voucher, $userId, $receivedItems) {
            foreach ($receivedItems as $itemData) {
                $item = $voucher->items()->find($itemData['item_id']);
                
                if (!$item) {
                    continue;
                }

                // Update received quantities
                $item->update([
                    'quantity_received' => $itemData['quantity_received'] ?? 0,
                    'quantity_rejected' => $itemData['quantity_rejected'] ?? 0,
                    'rejection_notes' => $itemData['rejection_notes'] ?? null,
                ]);

                // Update line total based on actual received quantity
                $item->update([
                    'line_total' => ($item->quantity_received * $item->unit_cost),
                ]);
            }

            // Mark voucher as received
            $voucher->update([
                'received_by' => $userId,
                'received_at' => now(),
                // TODO: Update status to "received"
            ]);

            return $this->repository->find($voucher->id);
        });
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
    public function rejectVoucher($id, string $reason): StockInputVoucher
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
