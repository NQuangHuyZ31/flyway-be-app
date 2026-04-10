<?php

namespace App\Repositories\StockInputVoucher;

use App\Models\StockInputVoucher;
use App\Repositories\BaseRepository;

class StockInputVoucherRepository extends BaseRepository implements StockInputVoucherRepositoryInterface
{
    public function getModel()
    {
        return StockInputVoucher::class;
    }

    public function getAllWithPagination($perPage)
    {
        return StockInputVoucher::with(['supplier', 'warehouse', 'section', 'creator', 'approver', 'items'])
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }

    public function getByWarehouse($warehouseId, $perPage)
    {
        return StockInputVoucher::with(['supplier', 'warehouse', 'section', 'creator', 'items'])
            ->byWarehouse($warehouseId)
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }

    public function getByStatus($statusId, $perPage)
    {
        return StockInputVoucher::with(['supplier', 'warehouse', 'section', 'creator', 'items'])
            ->byStatus($statusId)
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }
}
