<?php

namespace App\Repositories\StockOutputVoucher;

use App\Models\StockOutputVoucher;
use App\Repositories\BaseRepository;

class StockOutputVoucherRepository extends BaseRepository implements StockOutputVoucherRepositoryInterface
{
    public function getModel()
    {
        return StockOutputVoucher::class;
    }

    public function getAllWithPagination($perPage)
    {
        return StockOutputVoucher::with(['warehouse', 'section', 'creator', 'approver', 'items'])
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }

    public function getByWarehouse($warehouseId, $perPage)
    {
        return StockOutputVoucher::with(['warehouse', 'section', 'creator', 'items'])
            ->byWarehouse($warehouseId)
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }

    public function getByStatus($statusId, $perPage)
    {
        return StockOutputVoucher::with(['warehouse', 'section', 'creator', 'items'])
            ->byStatus($statusId)
            ->orderByDesc('created_at')
            ->paginate($perPage ?? 20);
    }
}
