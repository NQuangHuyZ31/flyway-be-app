<?php

namespace App\Repositories\StockInputVoucher;

use App\Repositories\RepositoryInterface;

interface StockInputVoucherRepositoryInterface extends RepositoryInterface
{
    public function getAllWithPagination($perPage);
    public function getByWarehouse($warehouseId, $perPage);
    public function getByStatus($statusId, $perPage);
}
