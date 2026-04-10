<?php

namespace App\Repositories\StockOutputVoucher;

use App\Repositories\RepositoryInterface;

interface StockOutputVoucherRepositoryInterface extends RepositoryInterface
{
    public function getAllWithPagination($perPage);
    public function getByWarehouse($warehouseId, $perPage);
    public function getByStatus($statusId, $perPage);
}
